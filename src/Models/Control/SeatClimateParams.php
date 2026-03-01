<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

use Byd\ApiClient\Models\BaseModel;

class SeatClimateParams extends BaseModel implements ControlParams
{
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
