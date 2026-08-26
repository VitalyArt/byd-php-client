<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Push notification state.
 */
class PushNotificationState extends BaseModel
{
    private bool $enabled = false;

    private ?string $deviceId = null;

    private ?string $deviceToken = null;

    private ?DateTimeInterface $lastUpdate = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $enabled = $data['enabled'] ?? $data['pushSwitch'] ?? false;
        $this->enabled = $enabled === true || $enabled === 1 || $enabled === '1';
        $this->deviceId = isset($data['deviceId']) ? (string) $data['deviceId'] : null;
        $this->deviceToken = isset($data['deviceToken']) ? (string) $data['deviceToken'] : null;

        if (isset($data['lastUpdate'])) {
            $this->lastUpdate = $this->parseTimestamp($data['lastUpdate']);
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
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function getDeviceId(): ?string
    {
        return $this->deviceId;
    }

    public function getDeviceToken(): ?string
    {
        return $this->deviceToken;
    }

    public function getLastUpdate(): ?DateTimeInterface
    {
        return $this->lastUpdate;
    }
}
