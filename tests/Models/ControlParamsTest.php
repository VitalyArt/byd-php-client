<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\ChargeChangeResult;
use Byd\ApiClient\Models\Control\BatteryHeatParams;
use Byd\ApiClient\Models\Control\ClimateStartParams;
use Byd\ApiClient\Models\Control\RemoteCommand;
use Byd\ApiClient\Models\RemoteControlResult;
use Byd\ApiClient\Models\SmartChargingSchedule;
use PHPUnit\Framework\TestCase;

class ControlParamsTest extends TestCase
{
    public function testClimateStartUsesBydWireKeysAndTemperatureScale(): void
    {
        $params = (new ClimateStartParams())
            ->setTemperature(21.0)
            ->setTimeSpan(3);

        self::assertSame([
            'cycleMode' => 2,
            'remoteMode' => 4,
            'airAccuracy' => 1,
            'airConditioningMode' => 1,
            'mainSettingTemp' => 7,
            'copilotSettingTemp' => 7,
            'timeSpan' => 3,
            'airSet' => null,
        ], $params->toControlParamsMap());
    }

    public function testBatteryHeatUsesExpectedWireKey(): void
    {
        $params = (new BatteryHeatParams())->setEnabled(true);

        self::assertSame(['batteryHeatSwitch' => 1], $params->toControlParamsMap());
    }

    public function testRemoteControlNormalizesResResponse(): void
    {
        $result = new RemoteControlResult(['res' => 2]);

        self::assertSame(1, $result->getControlState());
        self::assertTrue($result->isSuccess());
    }

    public function testSmartChargingSwitchStringZeroIsDisabled(): void
    {
        $schedule = new SmartChargingSchedule([
            'smartChargeDto' => ['smartChargeSwitch' => '0'],
        ]);

        self::assertFalse($schedule->isEnabled());
    }

    public function testRemoteCommandUsesApiValues(): void
    {
        self::assertSame('LOCKDOOR', RemoteCommand::LOCK->value);
        self::assertSame('OPENAIR', RemoteCommand::START_CLIMATE->value);
    }

    public function testChargeChangeSuccessRequiresTerminalResTwo(): void
    {
        self::assertTrue((new ChargeChangeResult(['res' => '2']))->isSuccess());
        self::assertFalse((new ChargeChangeResult(['res' => '3']))->isSuccess());
    }
}
