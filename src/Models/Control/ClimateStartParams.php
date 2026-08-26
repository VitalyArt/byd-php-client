<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

/**
 * Climate start parameters.
 */
class ClimateStartParams implements ControlParams
{
    private ?float $temperature = null;

    private ?float $copilotTemperature = null;

    private int $cycleMode = 2;

    private ?int $timeSpan = null;

    private int $remoteMode = 4;

    private int $airAccuracy = 1;

    private int $airConditioningMode = 1;

    private ?int $windLevel = null;

    /**
     * Convert to control parameters map.
     *
     * @return array<string, mixed>
     */
    public function toControlParamsMap(): array
    {
        $params = [
            'cycleMode' => $this->cycleMode,
            'remoteMode' => $this->remoteMode,
            'airAccuracy' => $this->airAccuracy,
            'airConditioningMode' => $this->airConditioningMode,
        ];

        if ($this->temperature !== null) {
            $params['mainSettingTemp'] = self::celsiusToScale($this->temperature);
            $params['copilotSettingTemp'] = self::celsiusToScale($this->copilotTemperature ?? $this->temperature);
        }

        if ($this->timeSpan !== null) {
            $params['timeSpan'] = $this->timeSpan;
        }

        if ($this->windLevel !== null) {
            $params['windLevel'] = $this->windLevel;
        }

        if ($this->remoteMode === 4) {
            $params['airSet'] = null;
        }

        return $params;
    }

    private static function celsiusToScale(float $temperature): int
    {
        return (int) round(($temperature - 15.0) + 1.0);
    }

    // Getters and setters
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    public function setTemperature(?float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getCopilotTemperature(): ?float
    {
        return $this->copilotTemperature;
    }

    public function setCopilotTemperature(?float $copilotTemperature): self
    {
        $this->copilotTemperature = $copilotTemperature;

        return $this;
    }

    public function getCycleMode(): int
    {
        return $this->cycleMode;
    }

    public function setCycleMode(int $cycleMode): self
    {
        $this->cycleMode = $cycleMode;

        return $this;
    }

    public function getTimeSpan(): ?int
    {
        return $this->timeSpan;
    }

    public function setTimeSpan(?int $timeSpan): self
    {
        $this->timeSpan = $timeSpan;

        return $this;
    }

    public function getRemoteMode(): int
    {
        return $this->remoteMode;
    }

    public function setRemoteMode(int $remoteMode): self
    {
        $this->remoteMode = $remoteMode;

        return $this;
    }

    public function getAirAccuracy(): int
    {
        return $this->airAccuracy;
    }

    public function setAirAccuracy(int $airAccuracy): self
    {
        $this->airAccuracy = $airAccuracy;

        return $this;
    }

    public function getAirConditioningMode(): int
    {
        return $this->airConditioningMode;
    }

    public function setAirConditioningMode(int $airConditioningMode): self
    {
        $this->airConditioningMode = $airConditioningMode;

        return $this;
    }

    public function getWindLevel(): ?int
    {
        return $this->windLevel;
    }

    public function setWindLevel(?int $windLevel): self
    {
        $this->windLevel = $windLevel;

        return $this;
    }
}
