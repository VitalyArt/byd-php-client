<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Dto\Protocol\ApiEnvelopeRequest;
use Byd\ApiClient\Enum\ApiErrorCode;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Exception\ApiException;
use Byd\ApiClient\Exception\ProtocolException;
use Byd\ApiClient\Exception\SessionExpiredException;
use Byd\ApiClient\Exception\UnsupportedFeatureException;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Serialization\ProtocolPayloadNormalizer;

use function is_array;
use function is_scalar;
use function is_string;

use Psr\Clock\ClockInterface;
use Throwable;

final readonly class ProtocolClient
{
    public function __construct(
        private ClientConfig $config,
        private SessionManager $sessions,
        private SecureTransportInterface $transport,
        private DtoSerializer $serializer,
        private ProtocolPayloadNormalizer $payloadNormalizer,
        private Cryptography $cryptography,
        private ClockInterface $clock,
        private NonceGeneratorInterface $nonceGenerator,
    ) {
    }

    /** @return array<array-key, mixed> */
    public function request(Endpoint $endpoint, object $request): array
    {
        $attempt = 0;
        while (true) {
            try {
                return $this->send($endpoint, $request, $this->sessions->current());
            } catch (SessionExpiredException $exception) {
                if ($attempt >= $this->config->authenticationRetry->maximumReauthentications) {
                    throw $exception;
                }

                ++$attempt;
                $this->sessions->refresh();
            }
        }
    }

    /** @return array<array-key, mixed> */
    private function send(Endpoint $endpoint, object $request, Session $session): array
    {
        $timestamp = (string) ($this->clock->now()->getTimestamp() * 1000);
        $device = $this->config->device;
        $imeiHash = $this->cryptography->md5($this->config->credentials->username);
        $inner = array_merge([
            'deviceType' => $device->deviceType,
            'imeiMD5' => $imeiHash,
            'networkType' => $device->networkType,
            'random' => $this->nonceGenerator->generate(),
            'timeStamp' => $timestamp,
            'version' => $this->config->protocol->appInnerVersion,
        ], $this->payloadNormalizer->normalize($request));

        $contentKey = $session->contentKey($this->cryptography);
        $encrypted = $this->cryptography->encrypt(json_encode($inner, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR), $contentKey);
        $signature = $this->cryptography->sign(array_merge($inner, [
            'countryCode' => $this->config->locale->countryCode,
            'identifier' => $session->userId,
            'imeiMD5' => $imeiHash,
            'language' => $this->config->locale->language,
            'reqTimestamp' => $timestamp,
        ]), $session->signingKey($this->cryptography));
        $arguments = [$this->config->locale->countryCode, $encrypted, $session->userId, $imeiHash, $this->config->locale->language, $timestamp, $signature, $device->osType, $device->imei, $device->mac, $device->model, $device->sdk, $device->manufacturer, $timestamp];
        $withoutCheckcode = new ApiEnvelopeRequest(...[...$arguments, '']);
        $outerFields = $this->serializer->normalize($withoutCheckcode);
        unset($outerFields['checkcode']);
        $envelope = new ApiEnvelopeRequest(...[...$arguments, $this->cryptography->checkcode($outerFields)]);
        $outer = $this->transport->send($endpoint, $envelope);
        $this->assertSuccess($outer, $endpoint);
        $encryptedResponse = $outer['respondData'] ?? null;
        if ($encryptedResponse === null) {
            return [];
        }

        if (!is_string($encryptedResponse)) {
            throw new ProtocolException("Invalid encrypted response from {$endpoint->name}.");
        }

        try {
            $plaintext = $this->cryptography->decrypt($encryptedResponse, $contentKey);
            if (trim($plaintext) === '') {
                return [];
            }

            $decoded = json_decode($plaintext, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            throw new ProtocolException("Unable to decode response from {$endpoint->name}.", $exception->getCode(), previous: $exception);
        }

        if (!is_array($decoded)) {
            throw new ProtocolException("Response from {$endpoint->name} is not an object or list.");
        }

        $this->assertSuccess($decoded, $endpoint);

        return $decoded;
    }

    /** @param array<array-key, mixed> $response */
    private function assertSuccess(array $response, Endpoint $endpoint): void
    {
        $rawCode = $response['code'] ?? null;
        if ($rawCode === null || !is_scalar($rawCode) || (string) $rawCode === '0') {
            return;
        }

        $code = is_numeric($rawCode) ? (int) $rawCode : 0;
        $known = ApiErrorCode::tryFrom($code);
        $rawMessage = $response['message'] ?? $response['msg'] ?? null;
        $message = is_scalar($rawMessage) ? (string) $rawMessage : "BYD API error {$code}";
        if ($known === ApiErrorCode::SESSION_EXPIRED) {
            throw new SessionExpiredException($message, $code);
        }

        if ($known === ApiErrorCode::ENDPOINT_NOT_SUPPORTED || $known === ApiErrorCode::VEHICLE_NOT_SUPPORTED) {
            throw new UnsupportedFeatureException($message, $code);
        }

        throw new ApiException($message, $code, $endpoint, $known);
    }
}
