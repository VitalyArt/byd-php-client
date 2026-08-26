<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Dto\Request\ChargingScheduleRequest;
use Byd\ApiClient\Dto\Request\ChargingToggleRequest;
use Byd\ApiClient\Dto\Request\StartChargingRequest;
use Byd\ApiClient\Dto\Request\VehicleRequest;
use Byd\ApiClient\Dto\Response\ChargingSchedule;
use Byd\ApiClient\Dto\Response\ChargingStatus;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\PollingExecutor;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function is_array;
use function is_string;

final readonly class ChargingService
{
    public function __construct(private Vin $vin, private ProtocolClient $protocol, private DtoSerializer $serializer, private PollingExecutor $polling)
    {
    }

    public function status(): ChargingStatus
    {
        return $this->serializer->denormalize($this->homepage(), ChargingStatus::class);
    }

    public function schedule(): ChargingSchedule
    {
        $raw = $this->homepage();
        $schedule = is_array($raw['smartChargeDto'] ?? null) ? $raw['smartChargeDto'] : $raw;

        return $this->serializer->denormalize($schedule, ChargingSchedule::class);
    }

    public function saveSchedule(ChargingScheduleRequest $request): CommandResult
    {
        return $this->serializer->denormalize($this->protocol->request(Endpoint::CHARGING_SAVE, $request), CommandResult::class);
    }

    public function setSmartCharging(bool $enabled): CommandResult
    {
        $raw = $this->protocol->request(Endpoint::CHARGING_TOGGLE, new ChargingToggleRequest($this->vin, $enabled ? '1' : '0'));

        return $this->serializer->denormalize($raw, CommandResult::class);
    }

    public function start(): CommandResult
    {
        $serial = null;

        return $this->polling->until(
            function () use (&$serial): CommandResult {
                $endpoint = $serial === null ? Endpoint::CHARGING_TOGGLE : Endpoint::CHARGING_RESULT;
                $request = $serial === null ? new StartChargingRequest($this->vin) : new VehicleRequest($this->vin, $serial);
                $raw = $this->protocol->request($endpoint, $request);
                $serial = is_string($raw['requestSerial'] ?? null) ? $raw['requestSerial'] : $serial;

                return $this->serializer->denormalize($raw, CommandResult::class);
            },
            static fn (CommandResult $result): bool => $result->result !== null && $result->result >= 2,
        );
    }

    /** @return array<array-key, mixed> */
    private function homepage(): array
    {
        return $this->protocol->request(Endpoint::CHARGING_HOME, new VehicleRequest($this->vin));
    }
}
