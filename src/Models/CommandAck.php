<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Command acknowledgment.
 */
class CommandAck extends BaseModel
{
    private string $resultCode = '';

    private string $resultMsg = '';

    private ?string $serialNumber = null;

    private ?DateTimeInterface $timestamp = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->resultCode = (string) ($data['resultCode'] ?? '');
        $this->resultMsg = (string) ($data['resultMsg'] ?? '');
        $this->serialNumber = isset($data['serialNumber']) ? (string) $data['serialNumber'] : null;

        if (isset($data['timestamp'])) {
            $this->timestamp = $this->parseTimestamp($data['timestamp']);
        }
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

    public function isSuccess(): bool
    {
        return $this->resultCode === 'success' || $this->resultCode === '0';
    }
}
