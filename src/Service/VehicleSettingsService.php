<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Dto\Request\RenameVehicleRequest;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

final readonly class VehicleSettingsService
{
    public function __construct(private Vin $vin, private ProtocolClient $protocol, private DtoSerializer $serializer)
    {
    }

    /** Change the user-visible vehicle alias. */
    public function rename(string $name): CommandResult
    {
        return $this->serializer->denormalize($this->protocol->request(Endpoint::VEHICLE_RENAME, new RenameVehicleRequest($this->vin, $name)), CommandResult::class);
    }
}
