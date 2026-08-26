<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use function array_slice;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Dto\Protocol\LoginEnvelopeRequest;
use Byd\ApiClient\Dto\Protocol\LoginInnerRequest;
use Byd\ApiClient\Dto\Response\AuthToken;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Exception\AuthenticationException;
use Byd\ApiClient\Serialization\DtoSerializer;

use function is_array;
use function is_scalar;
use function is_string;

use Psr\Clock\ClockInterface;
use Throwable;
use UnexpectedValueException;

final readonly class AuthenticationService
{
    public function __construct(
        private ClientConfig $config,
        private SecureTransportInterface $transport,
        private DtoSerializer $serializer,
        private Cryptography $cryptography,
        private ClockInterface $clock,
        private NonceGeneratorInterface $nonceGenerator,
    ) {
    }

    public function authenticate(): Session
    {
        $timestamp = (string) ($this->clock->now()->getTimestamp() * 1000);
        $device = $this->config->device;
        $protocol = $this->config->protocol;
        $credentials = $this->config->credentials;
        $imeiHash = $this->cryptography->md5($credentials->username);
        $inner = new LoginInnerRequest('0', '[1,2]', $protocol->appInnerVersion, $protocol->appVersion, $device->mobileBrand.$device->mobileModel, $device->deviceType, $imeiHash, '1', $device->mobileBrand, $device->mobileModel, $device->networkType, $device->osType, $device->osVersion, $this->nonceGenerator->generate(), $protocol->softType, $timestamp, $this->config->locale->timeZone);
        $innerFields = $this->serializer->normalize($inner);
        $encrypted = $this->cryptography->encrypt(json_encode($innerFields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $this->cryptography->loginKey($credentials->password));
        $signFields = array_merge($innerFields, [
            'appName' => $protocol->appName,
            'countryCode' => $this->config->locale->countryCode,
            'functionType' => 'pwdLogin',
            'identifier' => $credentials->username,
            'identifierType' => '0',
            'language' => $this->config->locale->language,
            'reqTimestamp' => $timestamp,
        ]);
        $signature = $this->cryptography->sign($signFields, $this->cryptography->md5($credentials->password));
        $arguments = [$protocol->appName, $this->config->locale->countryCode, $encrypted, 'pwdLogin', $credentials->username, '0', $imeiHash, '1', $this->config->locale->language, $timestamp, $signature, $credentials->password, $device->osType, $device->imei, $device->mac, $device->model, $device->sdk, $device->manufacturer, $timestamp];
        $withoutCheckcode = new LoginEnvelopeRequest(...[...$arguments, '']);
        $request = new LoginEnvelopeRequest(...[...$arguments, $this->cryptography->checkcode(array_slice($this->serializer->normalize($withoutCheckcode), 0, -1, true))]);
        $response = $this->transport->send(Endpoint::LOGIN, $request);

        $responseCode = $response['code'] ?? null;
        if (!is_scalar($responseCode) || (string) $responseCode !== '0' || !is_string($response['respondData'] ?? null)) {
            $responseMessage = $response['message'] ?? null;

            throw new AuthenticationException('BYD authentication failed: '.(is_scalar($responseMessage) ? (string) $responseMessage : 'invalid response'));
        }

        try {
            $decoded = json_decode($this->cryptography->decrypt($response['respondData'], $this->cryptography->loginKey($credentials->password)), true, flags: JSON_THROW_ON_ERROR);
            $tokenData = is_array($decoded) && is_array($decoded['token'] ?? null) ? $decoded['token'] : throw new UnexpectedValueException('Missing token.');
            $token = $this->serializer->denormalize($tokenData, AuthToken::class);
        } catch (Throwable $exception) {
            throw new AuthenticationException('Unable to decode authentication response.', $exception->getCode(), previous: $exception);
        }

        return new Session($token->userId, $token->signToken, $token->encryptionToken, $this->clock->now(), $this->config->sessionTtlSeconds);
    }
}
