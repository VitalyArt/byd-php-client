<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

use Byd\ApiClient\Models\BaseModel;

class ClimateScheduleParams extends BaseModel implements ControlParams
{
    private ?int $startHour = null;
    private ?int $startMinute = null;
    private ?int $endHour = null;
    private ?int $endMinute = null;
    private ?int $temperature = null;
    private ?int $seatHeating = null;
    private ?int $seatVentilation = null;
    private ?bool $steeringWheelHeating = null;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->startHour = isset($data['startHour']) ? (int) $data['startHour'] : null;
        $this->startMinute = isset($data['startMinute']) ? (int) $data['startMinute'] : null;
        $this->endHour = isset($data['endHour']) ? (int) $data['endHour'] : null;
        $this->endMinute = isset($data['endMinute']) ? (int) $data['endMinute'] : null;
        $this->temperature = isset($data['temperature']) ? (int) $data['temperature'] : null;
        $this->seatHeating = isset($data['seatHeating']) ? (int) $data['seatHeating'] : null;
        $this->seatVentilation = isset($data['seatVentilation']) ? (int) $data['seatVentilation'] : null;
        $this->steeringWheelHeating = isset($data['steeringWheelHeating']) ? (bool) $data['steeringWheelHeating'] : null;
    }

    /**
     * @return array<string, string>
     */
    public function toControlParamsMap(): array
    {
        $params = [];

        if ($this->startHour !== null) {
            $params['startHour'] = (string) $this->startHour;
        }

        if ($this->startMinute !== null) {
            $params['startMinute'] = (string) $this->startMinute;
        }

        if ($this->endHour !== null) {
            $params['endHour'] = (string) $this->endHour;
        }

        if ($this->endMinute !== null) {
            $params['endMinute'] = (string) $this->endMinute;
        }

        if ($this->temperature !== null) {
            $params['temperature'] = (string) $this->temperature;
        }

        if ($this->seatHeating !== null) {
            $params['seatHeating'] = (string) $this->seatHeating;
        }

        if ($this->seatVentilation !== null) {
            $params['seatVentilation'] = (string) $this->seatVentilation;
        }

        if ($this->steeringWheelHeating !== null) {
            $params['steeringWheelHeating'] = $this->steeringWheelHeating ? '1' : '0';
        }

        return $params;
    }

    public function getStartHour(): ?int
    {
        return $this->startHour;
    }

    public function setStartHour(?int $startHour): self
    {
        $this->startHour = $startHour;

        return $this;
    }

    public function getStartMinute(): ?int
    {
        return $this->startMinute;
    }

    public function setStartMinute(?int $startMinute): self
    {
        $this->startMinute = $startMinute;

        return $this;
    }

    public function getEndHour(): ?int
    {
        return $this->endHour;
    }

    public function setEndHour(?int $endHour): self
    {
        $this->endHour = $endHour;

        return $this;
    }

    public function getEndMinute(): ?int
    {
        return $this->endMinute;
    }

    public function setEndMinute(?int $endMinute): self
    {
        $this->endMinute = $endMinute;

        return $this;
    }

    public function getTemperature(): ?int
    {
        return $this->temperature;
    }

    public function setTemperature(?int $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    public function getSeatHeating(): ?int
    {
        return $this->seatHeating;
    }

    public function setSeatHeating(?int $seatHeating): self
    {
        $this->seatHeating = $seatHeating;

        return $this;
    }

    public function getSeatVentilation(): ?int
    {
        return $this->seatVentilation;
    }

    public function setSeatVentilation(?int $seatVentilation): self
    {
        $this->seatVentilation = $seatVentilation;

        return $this;
    }

    public function getSteeringWheelHeating(): ?bool
    {
        return $this->steeringWheelHeating;
    }

    public function setSteeringWheelHeating(?bool $steeringWheelHeating): self
    {
        $this->steeringWheelHeating = $steeringWheelHeating;

        return $this;
    }
}
