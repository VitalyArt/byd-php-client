<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

/**
 * Climate start parameters.
 */
class ClimateStartParams implements ControlParams
{
    private ?int $temperature = null;

    private bool $acOn = false;

    private bool $heating = false;

    private bool $defrost = false;

    private bool $frontDefrost = false;

    private bool $rearDefrost = false;

    /**
     * Convert to control parameters map.
     *
     * @return array<string, mixed>
     */
    public function toControlParamsMap(): array
    {
        $params = [];

        if ($this->temperature !== null) {
            $params['temperature'] = $this->temperature;
        }

        $params['acOn'] = $this->acOn ? 1 : 0;
        $params['heating'] = $this->heating ? 1 : 0;
        $params['defrost'] = $this->defrost ? 1 : 0;
        $params['frontDefrost'] = $this->frontDefrost ? 1 : 0;
        $params['rearDefrost'] = $this->rearDefrost ? 1 : 0;

        return $params;
    }

    // Getters and setters
    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(?int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function isAcOn(): bool
    {
        return $this->acOn;
    }

    public function setAcOn(bool $acOn): self
    {
        $this->acOn = $acOn;

        return $this;
    }

    public function isHeating(): bool
    {
        return $this->heating;
    }

    public function setHeating(bool $heating): self
    {
        $this->heating = $heating;

        return $this;
    }

    public function isDefrost(): bool
    {
        return $this->defrost;
    }

    public function setDefrost(bool $defrost): self
    {
        $this->defrost = $defrost;

        return $this;
    }

    public function isFrontDefrost(): bool
    {
        return $this->frontDefrost;
    }

    public function setFrontDefrost(bool $frontDefrost): self
    {
        $this->frontDefrost = $frontDefrost;

        return $this;
    }

    public function isRearDefrost(): bool
    {
        return $this->rearDefrost;
    }

    public function setRearDefrost(bool $rearDefrost): self
    {
        $this->rearDefrost = $rearDefrost;

        return $this;
    }
}
