<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Dto\Request\PushSwitchRequest;
use Byd\ApiClient\Dto\Request\VehicleRequest;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Dto\Response\PushNotificationState;
use Byd\ApiClient\Dto\Response\PushSwitch;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function is_array;

final readonly class NotificationService
{
    public function __construct(private Vin $vin, private ProtocolClient $protocol, private DtoSerializer $serializer)
    {
    }

    public function state(): PushNotificationState
    {
        $raw = $this->protocol->request(Endpoint::PUSH_GET, new VehicleRequest($this->vin));
        $switches = [];
        foreach ($raw as $item) {
            if (is_array($item)) {
                $switches[] = $this->serializer->denormalize($item, PushSwitch::class);
            }
        }

        return new PushNotificationState($switches, $raw);
    }

    public function setEnabled(bool $enabled): CommandResult
    {
        return $this->serializer->denormalize($this->protocol->request(Endpoint::PUSH_SET, new PushSwitchRequest($this->vin, PushSwitch::VEHICLE_STATUS_TYPE, $enabled ? '1' : '0')), CommandResult::class);
    }
}
