<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Crypto\Aes;
use Byd\ApiClient\Crypto\Hashing;
use Byd\ApiClient\Crypto\Signing;
use Byd\ApiClient\Exceptions\BydAuthenticationException;
use Byd\ApiClient\Models\AuthToken;

use function is_array;
use function sprintf;

/**
 * Login endpoint handler.
 */
class Login
{
    /**
     * Build the common outer fields from device profile.
     *
     * @return array<string, string>
     */
    private static function commonOuterFields(BydConfig $config): array
    {
        $device = $config->getDevice();

        return [
            'ostype' => $device->getOstype(),
            'imei' => $device->getImei(),
            'mac' => $device->getMac(),
            'model' => $device->getModel(),
            'sdk' => $device->getSdk(),
            'mod' => $device->getMod(),
        ];
    }

    /**
     * Build the outer payload for the login endpoint.
     *
     * @return array<string, mixed>
     */
    public static function buildLoginRequest(BydConfig $config, int $nowMs): array
    {
        $randomHex = strtoupper(bin2hex(random_bytes(16)));
        $reqTimestamp = (string) $nowMs;
        $serviceTime = (string) (int) (microtime(true) * 1000);

        $device = $config->getDevice();

        $inner = [
            'appInnerVersion' => $config->getAppInnerVersion(),
            'appVersion' => $config->getAppVersion(),
            'deviceName' => $device->getMobileBrand() . $device->getMobileModel(),
            'deviceType' => $device->getDeviceType(),
            'imeiMD5' => $device->getImeiMd5(),
            'isAuto' => $config->getIsAuto(),
            'mobileBrand' => $device->getMobileBrand(),
            'mobileModel' => $device->getMobileModel(),
            'networkType' => $device->getNetworkType(),
            'osType' => $device->getOsType(),
            'osVersion' => $device->getOsVersion(),
            'random' => $randomHex,
            'softType' => $config->getSoftType(),
            'timeStamp' => $reqTimestamp,
            'timeZone' => $config->getTimeZone(),
        ];

        $encryData = Aes::aesEncryptHex(
            json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            Hashing::pwdLoginKey($config->getPassword())
        );

        $passwordMd5 = Hashing::md5Hex($config->getPassword());

        $signFields = array_merge($inner, [
            'countryCode' => $config->getCountryCode(),
            'functionType' => 'pwdLogin',
            'identifier' => $config->getUsername(),
            'identifierType' => '0',
            'language' => $config->getLanguage(),
            'reqTimestamp' => $reqTimestamp,
        ]);

        $sign = Hashing::sha1Mixed(Signing::buildSignString($signFields, $passwordMd5));

        $outer = array_merge([
            'countryCode' => $config->getCountryCode(),
            'encryData' => $encryData,
            'functionType' => 'pwdLogin',
            'identifier' => $config->getUsername(),
            'identifierType' => '0',
            'imeiMD5' => $device->getImeiMd5(),
            'isAuto' => $config->getIsAuto(),
            'language' => $config->getLanguage(),
            'reqTimestamp' => $reqTimestamp,
            'sign' => $sign,
            'signKey' => $config->getPassword(),
            'serviceTime' => $serviceTime,
        ], self::commonOuterFields($config));

        $outer['checkcode'] = Hashing::computeCheckcode($outer);

        return $outer;
    }

    /**
     * Parse login response and extract the auth token.
     *
     * @param array<string, mixed> $outerResponse
     * @return AuthToken
     *
     * @throws BydAuthenticationException
     */
    public static function parseLoginResponse(array $outerResponse, string $password): AuthToken
    {
        if ((string) ($outerResponse['code'] ?? '') !== '0') {
            throw new BydAuthenticationException(
                sprintf(
                    'Login failed: code=%s message=%s',
                    $outerResponse['code'] ?? '',
                    $outerResponse['message'] ?? ''
                ),
                (string) ($outerResponse['code'] ?? ''),
                '/app/account/login'
            );
        }

        $respondData = $outerResponse['respondData'] ?? null;
        if (!$respondData) {
            throw new BydAuthenticationException(
                'Login response missing respondData',
                '',
                '/app/account/login'
            );
        }

        $plaintext = Aes::aesDecryptUtf8($respondData, Hashing::pwdLoginKey($password));
        $inner = json_decode($plaintext, true);

        if (!is_array($inner)) {
            throw new BydAuthenticationException(
                'Failed to decode login response',
                '',
                '/app/account/login'
            );
        }

        $token = $inner['token'] ?? null;
        if (!is_array($token) ||
            !isset($token['userId']) ||
            !isset($token['signToken']) ||
            !isset($token['encryToken'])) {
            throw new BydAuthenticationException(
                'Login response missing token fields',
                '',
                '/app/account/login'
            );
        }

        return new AuthToken([
            'userId' => (string) $token['userId'],
            'signToken' => (string) $token['signToken'],
            'encryToken' => (string) $token['encryToken'],
        ]);
    }
}
