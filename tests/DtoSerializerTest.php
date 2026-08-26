<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\Dto\Request\ChargingScheduleRequest;
use Byd\ApiClient\Dto\Request\TelemetryRequest;
use Byd\ApiClient\Dto\Response\ChargingSchedule;
use Byd\ApiClient\Dto\Response\ChargingStatus;
use Byd\ApiClient\Dto\Response\EnergyConsumption;
use Byd\ApiClient\Dto\Response\PushSwitch;
use Byd\ApiClient\Dto\Response\Vehicle;
use Byd\ApiClient\Dto\Response\VehicleTelemetry;
use Byd\ApiClient\Enum\ChargingConnectionState;
use Byd\ApiClient\Enum\ChargingState;
use Byd\ApiClient\Enum\DoorState;
use Byd\ApiClient\Enum\EnergyType;
use Byd\ApiClient\Enum\OnlineState;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Serialization\ProtocolPayloadNormalizer;
use Byd\ApiClient\Value\Vin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class DtoSerializerTest extends TestCase
{
    private DtoSerializer $serializer;

    private Vin $vin;

    protected function setUp(): void
    {
        $this->serializer = new DtoSerializer();
        $this->vin = new Vin('LGXCE6CB1N0000001');
    }

    public function testRequestUsesExactWireNames(): void
    {
        self::assertSame([
            'vin' => $this->vin->value,
            'energyType' => '0',
            'tboxVersion' => '3',
        ], $this->serializer->normalize(new TelemetryRequest($this->vin, EnergyType::ELECTRIC, '3')));
    }

    public function testProtocolNormalizerStringifiesScheduleValues(): void
    {
        $normalizer = new ProtocolPayloadNormalizer($this->serializer);
        self::assertSame('80', $normalizer->normalize(new ChargingScheduleRequest($this->vin, 80, 1, 2, 3, 4))['targetSoc']);
    }

    public function testResponseAliasesSentinelsEnumsAndRawArePreserved(): void
    {
        $dto = $this->serializer->denormalize([
            'onlineState' => '1',
            'backCover' => 1,
            'tempInCar' => -129,
            'newServerField' => 'kept',
        ], VehicleTelemetry::class);

        self::assertSame(OnlineState::ONLINE, $dto->onlineState);
        self::assertSame(DoorState::OPEN, $dto->trunk);
        self::assertNull($dto->interiorTemperature);
        self::assertSame('kept', $dto->raw['newServerField']);
    }

    public function testUnknownEnumFallsBackToUnknown(): void
    {
        $dto = $this->serializer->denormalize(['leftFrontDoor' => 999], VehicleTelemetry::class);
        self::assertSame(DoorState::UNKNOWN, $dto->leftFrontDoor);
    }

    public function testVehicleIsImmutableTypedDto(): void
    {
        $dto = $this->serializer->denormalize(['vin' => $this->vin->value, 'modelName' => 'SEAL'], Vehicle::class);
        self::assertSame('SEAL', $dto->modelName);
        self::assertTrue(new ReflectionClass($dto)->isReadOnly());
    }

    public function testZeroFilledRealtimeResponseIsNotReady(): void
    {
        $dto = $this->serializer->denormalize([
            'requestSerial' => 'serial',
            'elecPercent' => 0,
            'enduranceMileage' => 0,
            'time' => 0,
            'leftFrontTirepressure' => 0,
        ], VehicleTelemetry::class);

        self::assertFalse($dto->hasMeaningfulData());
    }

    public function testActualChargingPayloadFieldsAreMapped(): void
    {
        $status = $this->serializer->denormalize([
            'chargingState' => '15',
            'soc' => '69',
            'connectState' => '0',
            'evSocLimit' => '100',
        ], ChargingStatus::class);
        $schedule = $this->serializer->denormalize([
            'startChargeTime' => '23:00',
            'endChargeTime' => 'full',
            'status' => '0',
        ], ChargingSchedule::class);

        self::assertSame(ChargingState::NOT_CONNECTED, $status->state);
        self::assertSame(ChargingConnectionState::DISCONNECTED, $status->connectionState);
        self::assertSame(69.0, $status->stateOfCharge);
        self::assertSame(100, $status->electricSocLimit);
        self::assertSame('23:00', $schedule->startTime);
        self::assertFalse($schedule->isEnabled());
    }

    public function testNestedEnergyPayloadIsMapped(): void
    {
        $energy = $this->serializer->denormalize([
            'cumulativeEnergyConsumption' => [
                'totalMileage' => '5481',
                'avgEvConsumption' => '19.1',
                'evUnit' => 'kW·h/100km',
            ],
            'nearestEnergyConsumption' => [
                'avgEvConsumption' => '21.0',
                'evConsumption' => '10.5',
                'evValueUnit' => 'kW·h',
            ],
            'selfGraph' => [
                'energyConsumption' => ['21.1', '20.1'],
                'energyConsumptionUnit' => 'kW·h/100km',
            ],
        ], EnergyConsumption::class);

        self::assertSame(5481.0, $energy->cumulative?->totalMileage);
        self::assertSame(19.1, $energy->cumulative?->averageElectricConsumption);
        self::assertSame(21.0, $energy->recent?->averageElectricConsumption);
        self::assertSame(10.5, $energy->recent?->electricConsumption);
        self::assertSame(['21.1', '20.1'], $energy->vehicleGraph?->values);
    }

    public function testPushSwitchUsesActualWireFields(): void
    {
        $switch = $this->serializer->denormalize(['type' => '701', 'state' => '1'], PushSwitch::class);

        self::assertSame(PushSwitch::VEHICLE_STATUS_TYPE, $switch->type);
        self::assertTrue($switch->isEnabled());
    }
}
