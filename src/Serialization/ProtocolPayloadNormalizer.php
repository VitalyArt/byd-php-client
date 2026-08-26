<?php

declare(strict_types=1);

namespace Byd\ApiClient\Serialization;

use Byd\ApiClient\Dto\Request\BatteryHeatRequest;
use Byd\ApiClient\Dto\Request\ChargingScheduleRequest;
use Byd\ApiClient\Dto\Request\ClimateStartRequest;
use Byd\ApiClient\Dto\Request\RemoteControlRequest;
use Byd\ApiClient\Dto\Request\SeatClimateRequest;

use function is_array;

final readonly class ProtocolPayloadNormalizer
{
    public function __construct(private DtoSerializer $serializer)
    {
    }

    /** @return array<string, mixed> */
    public function normalize(object $request): array
    {
        $data = $this->serializer->normalize($request);

        if ($request instanceof ChargingScheduleRequest) {
            $data['targetSoc'] = (string) $request->targetSoc;
            $data['startHour'] = (string) $request->startHour;
            $data['startMinute'] = (string) $request->startMinute;
            $data['endHour'] = (string) $request->endHour;
            $data['endMinute'] = (string) $request->endMinute;
        }

        if ($request instanceof RemoteControlRequest && isset($data['controlParamsMap']) && is_array($data['controlParamsMap'])) {
            $data['controlParamsMap'] = json_encode($data['controlParamsMap'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        if ($request instanceof ClimateStartRequest) {
            if ($request->temperature !== null) {
                $data['mainSettingTemp'] = (int) round($request->temperature - 14.0);
                $data['copilotSettingTemp'] = (int) round(($request->passengerTemperature ?? $request->temperature) - 14.0);
            }
        }

        if ($request instanceof SeatClimateRequest) {
            if ($request->heating instanceof \Byd\ApiClient\Enum\SeatClimateLevel) {
                $data['seatHeating'] = $request->heating->commandValue();
            }

            if ($request->ventilation instanceof \Byd\ApiClient\Enum\SeatClimateLevel) {
                $data['seatVentilation'] = $request->ventilation->commandValue();
            }
        }

        if ($request instanceof BatteryHeatRequest) {
            $data['batteryHeat'] = $request->enabled ? '1' : '0';
        }

        return $data;
    }
}
