<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Dto\Request\ClimateScheduleCommand;
use Byd\ApiClient\Dto\Request\ClimateStartRequest;
use Byd\ApiClient\Dto\Request\VehicleRequest;
use Byd\ApiClient\Dto\Response\ClimateStatus;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Enum\RemoteCommand;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function is_array;

final readonly class ClimateService
{
    public function __construct(private Vin $vin, private ProtocolClient $protocol, private DtoSerializer $serializer, private ControlService $controls)
    {
    }

    public function status(): ClimateStatus
    {
        $raw = $this->protocol->request(Endpoint::HVAC_STATUS, new VehicleRequest($this->vin));
        $raw = is_array($raw['statusNow'] ?? null) ? $raw['statusNow'] : $raw;

        return $this->serializer->denormalize($raw, ClimateStatus::class);
    }

    public function start(ClimateStartRequest $request): CommandResult
    {
        return $this->controls->executeDto(RemoteCommand::START_CLIMATE, $request);
    }

    public function stop(): CommandResult
    {
        return $this->controls->execute(RemoteCommand::STOP_CLIMATE);
    }

    public function schedule(ClimateScheduleCommand $request): CommandResult
    {
        return $this->controls->executeDto(RemoteCommand::SCHEDULE_CLIMATE, $request);
    }
}
