<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Dto\Request\EnergyRequest;
use Byd\ApiClient\Dto\Request\TelemetryRequest;
use Byd\ApiClient\Dto\Request\VehicleRequest;
use Byd\ApiClient\Dto\Response\EnergyConsumption;
use Byd\ApiClient\Dto\Response\GpsPosition;
use Byd\ApiClient\Dto\Response\VehicleTelemetry;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Enum\EnergyType;
use Byd\ApiClient\Enum\PowerType;
use Byd\ApiClient\PollingExecutor;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function is_array;
use function is_string;

final readonly class TelemetryService
{
    public function __construct(private Vin $vin, private ClientConfig $config, private VehicleService $vehicles, private ProtocolClient $protocol, private DtoSerializer $serializer, private PollingExecutor $polling)
    {
    }

    public function realtime(): VehicleTelemetry
    {
        $vehicle = $this->vehicles->get($this->vin);
        $energy = EnergyType::fromVehicleLabel($vehicle->energyType ?? 'EV');
        $serial = null;

        return $this->polling->until(
            function () use (&$serial, $energy): VehicleTelemetry {
                $endpoint = $serial === null ? Endpoint::REALTIME_REQUEST : Endpoint::REALTIME_RESULT;
                $raw = $this->protocol->request($endpoint, new TelemetryRequest($this->vin, $energy, $this->config->protocol->tboxVersion, $serial));
                $serial = is_string($raw['requestSerial'] ?? null) ? $raw['requestSerial'] : $serial;

                return $this->serializer->denormalize($raw, VehicleTelemetry::class);
            },
            static fn (VehicleTelemetry $result): bool => $result->hasMeaningfulData(),
        );
    }

    public function gps(): GpsPosition
    {
        $serial = null;

        return $this->polling->until(
            function () use (&$serial): GpsPosition {
                $endpoint = $serial === null ? Endpoint::GPS_REQUEST : Endpoint::GPS_RESULT;
                $raw = $this->protocol->request($endpoint, new VehicleRequest($this->vin, $serial));
                $serial = is_string($raw['requestSerial'] ?? null) ? $raw['requestSerial'] : $serial;
                $position = is_array($raw['data'] ?? null) ? array_merge($raw['data'], ['requestSerial' => $serial]) : $raw;

                return $this->serializer->denormalize($position, GpsPosition::class);
            },
            static fn (GpsPosition $result): bool => $result->latitude !== null && $result->longitude !== null,
        );
    }

    public function energyConsumption(): EnergyConsumption
    {
        $vehicle = $this->vehicles->get($this->vin);
        $label = $vehicle->energyType ?? 'EV';
        $model = $vehicle?->externalModelType;
        $raw = $this->protocol->request(Endpoint::ENERGY, new EnergyRequest($this->vin, PowerType::fromVehicleLabel($label), model: $model));

        return $this->serializer->denormalize($raw, EnergyConsumption::class);
    }
}
