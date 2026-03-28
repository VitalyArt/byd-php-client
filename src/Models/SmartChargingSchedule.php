<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Smart charging schedule.
 */
class SmartChargingSchedule extends BaseModel
{
    private ?int $targetSoc = null;

    private ?int $startHour = null;

    private ?int $startMinute = null;

    private ?int $endHour = null;

    private ?int $endMinute = null;

    private bool $enabled = false;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->targetSoc = isset($data['targetSoc']) ? (int) $data['targetSoc'] : null;
        $this->startHour = isset($data['startHour']) ? (int) $data['startHour'] : null;
        $this->startMinute = isset($data['startMinute']) ? (int) $data['startMinute'] : null;
        $this->endHour = isset($data['endHour']) ? (int) $data['endHour'] : null;
        $this->endMinute = isset($data['endMinute']) ? (int) $data['endMinute'] : null;
        $this->enabled = (bool) ($data['enabled'] ?? false);
    }

    // Getters
    public function getTargetSoc(): ?int
    {
        return $this->targetSoc;
    }

    public function getStartHour(): ?int
    {
        return $this->startHour;
    }

    public function getStartMinute(): ?int
    {
        return $this->startMinute;
    }

    public function getEndHour(): ?int
    {
        return $this->endHour;
    }

    public function getEndMinute(): ?int
    {
        return $this->endMinute;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    // Setters
    public function setTargetSoc(?int $targetSoc): self
    {
        $this->targetSoc = $targetSoc;

        return $this;
    }

    public function setStartHour(?int $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function setStartMinute(?int $startMinute): self
    {
        $this->startMinute = $startMinute;

        return $this;
    }

    public function setEndHour(?int $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function setEndMinute(?int $endMinute): self
    {
        $this->endMinute = $endMinute;

        return $this;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
