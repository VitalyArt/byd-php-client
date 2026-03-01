<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Crypto\Aes;
use Byd\ApiClient\Crypto\Hashing;
use Byd\ApiClient\Crypto\Signing;
use Byd\ApiClient\Exceptions\BydApiException;
use Byd\ApiClient\Exceptions\BydSessionExpiredException;
use Byd\ApiClient\Exceptions\BydVehicleNotSupportedException;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function in_array;
use function is_array;

class TokenJson
{
    private const ENDPOINT_NOT_SUPPORTED_CODES = [1004, 1005];

    /**
     * Post a token-authenticated JSON request to a BYD endpoint.
     *
     * @param array<string, mixed> $inner
     * @param array<int> $extraErrorCodes
     *
     * @return array<string, mixed>|list<mixed>|null
     */
    public static function postTokenJson(
        string $endpoint,
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        array $inner,
        ?int $nowMs = null,
        ?string $vin = null,
        ?string $userType = null,
        array $extraErrorCodes = []
    ) {
        if ($nowMs === null) {
            $nowMs = (int) (microtime(true) * 1000);
        }

        $reqTimestamp = (string) $nowMs;

        // Get keys from session
        $contentKey = $session->contentKey();
        $signKey = $session->signKey();

        // Encrypt inner payload
        $encryData = Aes::aesEncryptHex(
            json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            $contentKey
        );

        // Build sign fields
        $signFields = array_merge($inner, [
            'countryCode' => $config->getCountryCode(),
            'identifier' => $session->getUserId(),
            'imeiMD5' => $config->getDevice()->getImeiMd5(),
            'language' => $config->getLanguage(),
            'reqTimestamp' => $reqTimestamp,
        ]);

        $signString = Signing::buildSignString($signFields, $signKey);
        $sign = Hashing::sha1Mixed($signString);

        // Build outer payload
        $outer = [
            'countryCode' => $config->getCountryCode(),
            'encryData' => $encryData,
            'identifier' => $session->getUserId(),
            'imeiMD5' => $config->getDevice()->getImeiMd5(),
            'language' => $config->getLanguage(),
            'reqTimestamp' => $reqTimestamp,
            'sign' => $sign,
            'ostype' => $config->getDevice()->getOstype(),
            'imei' => $config->getDevice()->getImei(),
            'mac' => $config->getDevice()->getMac(),
            'model' => $config->getDevice()->getModel(),
            'sdk' => $config->getDevice()->getSdk(),
            'mod' => $config->getDevice()->getMod(),
            'serviceTime' => (string) (int) (microtime(true) * 1000),
        ];

        if ($userType !== null) {
            $outer['userType'] = $userType;
        }

        // Add checkcode at the end
        $outer['checkcode'] = Hashing::computeCheckcode($outer);

        // Send request
        try {
            $decoded = $transport->postSecure($endpoint, $outer);
        } catch (BydApiException $e) {
            // Re-throw certain codes as specialized exceptions
            if ($e->getCode() === '1017') {
                throw new BydSessionExpiredException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }

            if (in_array($e->getCode(), self::ENDPOINT_NOT_SUPPORTED_CODES, true)) {
                throw new BydVehicleNotSupportedException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }

            if (in_array($e->getCode(), $extraErrorCodes, true)) {
                throw $e; // Let caller handle it
            }

            throw $e;
        }

        // Extract inner response payload
        $payload = $decoded['respondData'] ?? null;

        $plaintext = Aes::aesDecryptUtf8($payload, $contentKey);
        $inner = json_decode($plaintext, true);

        // Check for explicit error code in response
        if (is_array($payload) && isset($payload['code']) && $payload['code'] !== '0') {
            $errorCode = (int) $payload['code'];
            $errorMessage = $payload['message'] ?? $payload['msg'] ?? ('API error ' . $errorCode);

            // Specialized error handling
            if ($errorCode === 1017) {
                throw new BydSessionExpiredException($errorMessage, $errorCode, $endpoint);
            }

            if (in_array($errorCode, self::ENDPOINT_NOT_SUPPORTED_CODES, true)) {
                throw new BydVehicleNotSupportedException($errorMessage, $errorCode, $endpoint);
            }

            if (in_array($errorCode, $extraErrorCodes, true)) {
                throw new BydApiException($errorMessage, $errorCode, $endpoint);
            }

            throw new BydApiException($errorMessage, $errorCode, $endpoint);
        }

        return $inner;
    }
}
