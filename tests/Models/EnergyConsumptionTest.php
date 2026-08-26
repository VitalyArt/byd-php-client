<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\EnergyConsumption;
use PHPUnit\Framework\TestCase;

class EnergyConsumptionTest extends TestCase
{
    public function testParsesNestedEnergyEndpointResponse(): void
    {
        $energy = new EnergyConsumption([
            'cumulativeEnergyConsumption' => [
                'totalMileage' => '12345.6',
                'mileageUnit' => 'km',
                'avgEvConsumption' => '16.2',
                'evUnit' => 'kW·h/100km',
            ],
            'nearestEnergyConsumption' => [
                'avgEvConsumption' => '14.8',
                'evConsumption' => '7.4',
                'evUnit' => 'kW·h/100km',
                'evValueUnit' => 'kW·h',
            ],
        ]);

        self::assertSame(12345.6, $energy->getTotalMileage());
        self::assertSame('km', $energy->getMileageUnit());
        self::assertSame(16.2, $energy->getCumulativeAverageEvConsumption());
        self::assertSame(14.8, $energy->getLast50kmAverageEvConsumption());
        self::assertSame(7.4, $energy->getLast50kmEvConsumption());
        self::assertSame('kWh/100km', $energy->getLast50kmEvUnit());
        self::assertSame('kWh', $energy->getLast50kmEvValueUnit());
    }
}
