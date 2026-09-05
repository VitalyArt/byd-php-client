<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Dto\Request\EmptyRequest;
use Byd\ApiClient\Dto\Response\Vehicle;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function is_array;

final class VehicleService
{
    /** @var array<string, Vehicle> */
    private array $cache = [];

    public function __construct(private readonly ProtocolClient $protocol, private readonly DtoSerializer $serializer)
    {
    }

    /**
     * Return all vehicles associated with the authenticated account.
     *
     * @return list<Vehicle>
     */
    public function all(): array
    {
        $response = $this->protocol->request(Endpoint::VEHICLES, new EmptyRequest());
        $vehicles = [];
        foreach ($response as $item) {
            if (is_array($item)) {
                $vehicle = $this->serializer->denormalize($item, Vehicle::class);
                $vehicles[] = $vehicle;
                $this->cache[$vehicle->vin->value] = $vehicle;
            }
        }

        return $vehicles;
    }

    /** Find a vehicle by VIN, loading the account list if necessary. */
    public function get(Vin $vin): ?Vehicle
    {
        if (!isset($this->cache[$vin->value])) {
            $this->all();
        }

        return $this->cache[$vin->value] ?? null;
    }
}
