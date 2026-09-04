<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use function abs;

use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\WatchTransportInterface;
use Byd\ApiClient\Crypto\WatchCryptography;
use Byd\ApiClient\Dto\Protocol\WatchAccountRequest;
use Byd\ApiClient\Dto\Protocol\WatchCommonRequest;
use Byd\ApiClient\Dto\Response\WatchBluetoothInfo;
use Byd\ApiClient\Dto\Response\WatchQrSession;
use Byd\ApiClient\Dto\Response\WatchQrStatusResponse;
use Byd\ApiClient\Dto\Response\WatchServerTime;
use Byd\ApiClient\Dto\Response\WatchTokenInfo;
use Byd\ApiClient\Dto\Response\WatchTokenResponse;
use Byd\ApiClient\Dto\Response\WatchVehicleConfiguration;
use Byd\ApiClient\Enum\WatchEndpoint;
use Byd\ApiClient\Enum\WatchQrStatus;
use Byd\ApiClient\Exception\ProtocolException;
use Byd\ApiClient\Exception\WatchApiException;
use Byd\ApiClient\Serialization\DtoSerializer;

use function is_scalar;

use Psr\Clock\ClockInterface;

final class WatchProtocolClient
{
    private int $serverOffsetMilliseconds = 0;

    public function __construct(
        private readonly WatchClientConfig $config,
        private readonly WatchTransportInterface $transport,
        private readonly DtoSerializer $serializer,
        private readonly WatchCryptography $cryptography,
        private readonly ClockInterface $clock,
        private readonly NonceGeneratorInterface $nonceGenerator,
    ) {
    }

    public function createQrSession(): WatchQrSession
    {
        $createdAt = $this->timestampMilliseconds();
        $data = $this->accountRequest(WatchEndpoint::CREATE_QR_CODE, null, $createdAt);
        $status = WatchQrStatus::tryFrom($this->integerField($data, 'status', -1)) ?? WatchQrStatus::UNKNOWN;
        $watchImei = $this->requiredString($data, 'watchImei', WatchEndpoint::CREATE_QR_CODE);
        $uuid = $this->requiredString($data, 'uuid', WatchEndpoint::CREATE_QR_CODE);

        return new WatchQrSession(
            $watchImei,
            $uuid,
            $status,
            $this->cryptography->qrPayload($watchImei, $uuid, $this->config->locale->countryCode),
            $createdAt,
        );
    }

    public function checkQrSession(WatchQrSession $session): WatchQrStatusResponse
    {
        $data = $this->accountRequest(WatchEndpoint::CHECK_QR_CODE, $session->uuid);

        return $this->serializer->denormalize($data, WatchQrStatusResponse::class);
    }

    public function gainToken(WatchQrSession $session): WatchTokenResponse
    {
        $data = $this->accountRequest(WatchEndpoint::GAIN_TOKEN, $session->uuid);

        return $this->serializer->denormalize($data, WatchTokenResponse::class);
    }

    public function gainVehicle(WatchTokenInfo $token): WatchVehicleConfiguration
    {
        $data = $this->commonRequest(WatchEndpoint::GAIN_VEHICLE, $token);

        return $this->serializer->denormalize($data, WatchVehicleConfiguration::class);
    }

    public function gainBluetooth(WatchTokenInfo $token): WatchBluetoothInfo
    {
        $data = $this->commonRequest(WatchEndpoint::GAIN_BLUETOOTH, $token);

        return $this->serializer->denormalize($data, WatchBluetoothInfo::class);
    }

    public function logout(WatchTokenInfo $token): void
    {
        $this->commonRequest(WatchEndpoint::LOGOUT, $token);
    }

    public function synchronizeServerTime(): WatchServerTime
    {
        $data = $this->accountRequest(WatchEndpoint::SERVER_TIME);
        $serverTime = $this->serializer->denormalize($data, WatchServerTime::class);
        $offset = $serverTime->serverTime - $this->localTimestampMilliseconds();
        $this->serverOffsetMilliseconds = abs($offset) > 10000 ? $offset : 0;

        return $serverTime;
    }

    /** @return array<string, mixed> */
    private function accountRequest(WatchEndpoint $endpoint, ?string $uuid = null, ?int $timestamp = null): array
    {
        $timestamp ??= $this->timestampMilliseconds();
        $timestampString = (string) $timestamp;
        $device = $this->config->device;
        $inner = match ($endpoint) {
            WatchEndpoint::CREATE_QR_CODE => [
                'timeStamp' => $timestampString,
                'random' => $this->nonceGenerator->generate(),
                'networkType' => $device->networkType,
                'version' => $device->appVersion,
            ],
            WatchEndpoint::CHECK_QR_CODE => [
                'timeStamp' => $timestampString,
                'random' => $this->nonceGenerator->generate(),
                'networkType' => $device->networkType,
                'version' => $device->appVersion,
                'uuid' => $uuid ?? throw new ProtocolException('Watch QR UUID is required.'),
            ],
            WatchEndpoint::GAIN_TOKEN => [
                'timeStamp' => $timestampString,
                'uuid' => $uuid ?? throw new ProtocolException('Watch QR UUID is required.'),
                'timeZone' => $this->config->locale->timeZone,
            ],
            WatchEndpoint::SERVER_TIME => [
                'timeStamp' => $timestampString,
                'random' => $this->nonceGenerator->generate(),
                'networkType' => $device->networkType,
                'deviceType' => '0',
                'version' => $device->appVersion,
            ],
            default => throw new ProtocolException("{$endpoint->name} is not a pre-login endpoint."),
        };
        $metadata = $this->accountMetadata($timestampString);
        $key = $this->cryptography->md5($this->config->locale->countryCode);
        $request = new WatchAccountRequest(
            identifier: $metadata['identifier'],
            watchImei: $metadata['watchImei'],
            watchModel: $metadata['watchModel'],
            sign: $this->cryptography->sign([...$inner, ...$metadata], $this->config->locale->countryCode),
            watchBrand: $metadata['watchBrand'],
            requestTimestamp: $metadata['reqTimestamp'],
            watchName: $metadata['watchName'],
            watchAppVersion: $metadata['watchAppVersion'],
            watchOs: $metadata['watchOs'],
            language: $metadata['language'],
            countryCode: $metadata['countryCode'],
            encryptedData: $this->cryptography->encryptFields($inner, $key),
        );

        return $this->send($endpoint, $request, $key);
    }

    /** @return array<string, mixed> */
    private function commonRequest(WatchEndpoint $endpoint, WatchTokenInfo $token): array
    {
        $timestamp = (string) $this->timestampMilliseconds();
        $device = $this->config->device;
        $inner = match ($endpoint) {
            WatchEndpoint::GAIN_VEHICLE, WatchEndpoint::GAIN_BLUETOOTH => [
                'timeStamp' => $timestamp,
                'random' => $this->nonceGenerator->generate(),
                'vin' => $token->vin,
                'deviceType' => '0',
                'appVersion' => '2',
            ],
            WatchEndpoint::LOGOUT => [
                'timeStamp' => $timestamp,
                'random' => $this->nonceGenerator->generate(),
                'watchImei' => $device->watchImei,
                'deviceType' => '0',
                'networkType' => $device->networkType,
                'version' => $device->appVersion,
            ],
            default => throw new ProtocolException("{$endpoint->name} is not a post-login endpoint."),
        };
        $metadata = $this->commonMetadata($timestamp, $token);
        $contentKey = $this->cryptography->md5($token->encryptionToken);
        $request = new WatchCommonRequest(
            identifier: $metadata['identifier'],
            watchImei: $metadata['watchImei'],
            watchModel: $metadata['watchModel'],
            watchName: $metadata['watchName'],
            watchBrand: $metadata['watchBrand'],
            watchAppVersion: $metadata['watchAppVersion'],
            watchOs: $metadata['watchOs'],
            requestTimestamp: $metadata['reqTimestamp'],
            language: $metadata['language'],
            countryCode: $metadata['countryCode'],
            userType: $metadata['userType'],
            encryptedData: $this->cryptography->encryptFields($inner, $contentKey),
            sign: $this->cryptography->sign([...$inner, ...$metadata], $token->signToken),
        );

        return $this->send($endpoint, $request, $contentKey);
    }

    /**
     * @return array{identifier: string, watchImei: string, watchModel: string, watchBrand: string, reqTimestamp: string, watchName: string, watchAppVersion: string, watchOs: string, language: string, countryCode: string}
     */
    private function accountMetadata(string $timestamp): array
    {
        $device = $this->config->device;

        return [
            'identifier' => $this->config->locale->countryCode,
            'watchImei' => $device->watchImei,
            'watchModel' => $device->model,
            'watchBrand' => $device->brand,
            'reqTimestamp' => $timestamp,
            'watchName' => $device->watchName(),
            'watchAppVersion' => $device->appVersion,
            'watchOs' => $device->watchOs,
            'language' => $this->config->locale->language,
            'countryCode' => $this->config->locale->countryCode,
        ];
    }

    /**
     * @return array{identifier: string, watchImei: string, watchModel: string, watchName: string, watchBrand: string, watchAppVersion: string, watchOs: string, reqTimestamp: string, language: string, countryCode: string, userType: string}
     */
    private function commonMetadata(string $timestamp, WatchTokenInfo $token): array
    {
        return [
            ...$this->accountMetadata($timestamp),
            'identifier' => $token->userId,
            'userType' => $token->userType,
        ];
    }

    /** @return array<string, mixed> */
    private function send(WatchEndpoint $endpoint, object $request, string $key): array
    {
        $envelope = $this->transport->send($endpoint, $request);
        $code = $envelope['code'] ?? null;
        if ($code !== null && (!is_scalar($code) || (string) $code !== '0')) {
            $numericCode = is_scalar($code) && is_numeric($code) ? (int) $code : 0;
            $message = is_scalar($envelope['message'] ?? null) ? (string) $envelope['message'] : "BYD watch API error {$numericCode}";

            throw new WatchApiException($message, $numericCode, $endpoint);
        }

        return $this->cryptography->decryptResponse($envelope['respondData'] ?? null, $key);
    }

    /** @param array<string, mixed> $data */
    private function requiredString(array $data, string $field, WatchEndpoint $endpoint): string
    {
        $value = $data[$field] ?? null;
        if (!is_scalar($value) || (string) $value === '') {
            throw new ProtocolException("Missing {$field} in {$endpoint->name} response.");
        }

        return (string) $value;
    }

    /** @param array<string, mixed> $data */
    private function integerField(array $data, string $field, int $default): int
    {
        $value = $data[$field] ?? null;

        return is_scalar($value) && is_numeric($value) ? (int) $value : $default;
    }

    private function timestampMilliseconds(): int
    {
        return $this->localTimestampMilliseconds() + $this->serverOffsetMilliseconds;
    }

    private function localTimestampMilliseconds(): int
    {
        return (int) $this->clock->now()->format('Uv');
    }
}
