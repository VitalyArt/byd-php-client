<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Remote control result.
 */
class RemoteControlResult extends BaseModel
{
    private string $resultCode = '';

    private string $resultMsg = '';

    private ?string $serialNumber = null;

    private ?DateTimeInterface $timestamp = null;

    private ?string $commandType = null;

    private ?string $vin = null;

    private ?string $uuid = null;

    private array $responseData = [];

    private ?int $controlState = null;

    private ?int $res = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->resultCode = (string) ($data['resultCode'] ?? '');
        $this->resultMsg = (string) ($data['resultMsg'] ?? '');
        $this->serialNumber = isset($data['serialNumber']) ? (string) $data['serialNumber'] : null;
        $this->commandType = isset($data['commandType']) ? (string) $data['commandType'] : null;
        $this->vin = isset($data['vin']) ? (string) $data['vin'] : null;
        $this->uuid = isset($data['uuid']) ? (string) $data['uuid'] : null;
        $this->res = isset($data['res']) ? (int) $data['res'] : null;
        $this->controlState = isset($data['controlState']) ? (int) $data['controlState'] : match ($this->res) {
            1 => 0,
            2 => 1,
            default => $this->res !== null ? 2 : null,
        };

        if (isset($data['timestamp'])) {
            $this->timestamp = $this->parseTimestamp($data['timestamp']);
        }

        $this->responseData = (array) ($data['responseData'] ?? []);
    }

    private function parseTimestamp($timestamp): ?DateTimeInterface
    {
        if ($timestamp === null) {
            return null;
        }

        $ts = (int) $timestamp;
        // Threshold to distinguish seconds from milliseconds.
        if ($ts >= 1000000000000) {
            $ts = (int) ($ts / 1000);
        }

        return DateTimeImmutable::createFromFormat('U', (string) $ts);
    }

    // Getters
    public function getResultCode(): string
    {
        return $this->resultCode;
    }

    public function getResultMsg(): string
    {
        return $this->resultMsg;
    }

    public function getSerialNumber(): ?string
    {
        return $this->serialNumber;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getCommandType(): ?string
    {
        return $this->commandType;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    public function getControlState(): ?int
    {
        return $this->controlState;
    }

    public function getRes(): ?int
    {
        return $this->res;
    }

    public function isSuccess(): bool
    {
        return $this->controlState === 1 || $this->resultCode === 'success' || $this->resultCode === '0';
    }
}
