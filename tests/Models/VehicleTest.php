<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\EmpowerRange;
use Byd\ApiClient\Models\Vehicle;
use PHPUnit\Framework\TestCase;

class VehicleTest extends TestCase
{
    // Default values when data is missing

    public function testDefaultsWhenEmpty(): void
    {
        $vehicle = new Vehicle([]);

        $this->assertSame('', $vehicle->getVin());
        $this->assertSame('', $vehicle->getModelName());
        $this->assertSame('', $vehicle->getBrandName());
        $this->assertSame('', $vehicle->getEnergyType());
        $this->assertSame('', $vehicle->getAutoAlias());
        $this->assertSame('', $vehicle->getAutoPlate());
        $this->assertSame('', $vehicle->getPicMainUrl());
        $this->assertSame('', $vehicle->getPicSetUrl());
        $this->assertSame('', $vehicle->getOutModelType());
        $this->assertNull($vehicle->getTotalMileage());
        $this->assertNull($vehicle->getModelId());
        $this->assertNull($vehicle->getCarType());
        $this->assertFalse($vehicle->isDefaultCar());
        $this->assertNull($vehicle->getEmpowerType());
        $this->assertNull($vehicle->getPermissionStatus());
        $this->assertSame('', $vehicle->getTboxVersion());
        $this->assertSame('', $vehicle->getVehicleState());
        $this->assertNull($vehicle->getAutoBoughtTime());
        $this->assertNull($vehicle->getYunActiveTime());
        $this->assertNull($vehicle->getEmpowerId());
        $this->assertSame([], $vehicle->getRangeDetailList());
    }

    // Basic string fields

    public function testStringFieldsPopulation(): void
    {
        $vehicle = new Vehicle([
            'vin' => 'VIN12345678901234',
            'modelName' => 'BYD SEAL',
            'brandName' => 'BYD',
            'energyType' => 'EV',
            'autoAlias' => 'My Car',
            'autoPlate' => 'AB-123-CD',
            'picMainUrl' => 'https://example.com/main.jpg',
            'picSetUrl' => 'https://example.com/set.jpg',
            'outModelType' => 'SEDAN',
            'tboxVersion' => '1.0.0',
            'vehicleState' => 'ACTIVE',
        ]);

        $this->assertSame('VIN12345678901234', $vehicle->getVin());
        $this->assertSame('BYD SEAL', $vehicle->getModelName());
        $this->assertSame('BYD', $vehicle->getBrandName());
        $this->assertSame('EV', $vehicle->getEnergyType());
        $this->assertSame('My Car', $vehicle->getAutoAlias());
        $this->assertSame('AB-123-CD', $vehicle->getAutoPlate());
        $this->assertSame('https://example.com/main.jpg', $vehicle->getPicMainUrl());
        $this->assertSame('https://example.com/set.jpg', $vehicle->getPicSetUrl());
        $this->assertSame('SEDAN', $vehicle->getOutModelType());
        $this->assertSame('1.0.0', $vehicle->getTboxVersion());
        $this->assertSame('ACTIVE', $vehicle->getVehicleState());
    }

    // Numeric fields

    public function testNumericFieldsPopulation(): void
    {
        $vehicle = new Vehicle([
            'totalMileage' => '12500.5',
            'modelId' => 42,
            'carType' => 1,
            'defaultCar' => true,
            'empowerType' => 1,
            'permissionStatus' => 0,
            'empowerId' => 100,
        ]);

        $this->assertSame(12500.5, $vehicle->getTotalMileage());
        $this->assertSame(42, $vehicle->getModelId());
        $this->assertSame(1, $vehicle->getCarType());
        $this->assertTrue($vehicle->isDefaultCar());
        $this->assertSame(1, $vehicle->getEmpowerType());
        $this->assertSame(0, $vehicle->getPermissionStatus());
        $this->assertSame(100, $vehicle->getEmpowerId());
    }

    // isShared

    public function testIsSharedWhenEmpowerTypeNegative(): void
    {
        $vehicle = new Vehicle(['empowerType' => -1]);
        $this->assertTrue($vehicle->isShared());
    }

    public function testIsSharedWhenEmpowerTypePositive(): void
    {
        $vehicle = new Vehicle(['empowerType' => 1]);
        $this->assertFalse($vehicle->isShared());
    }

    public function testIsSharedWhenEmpowerTypeNull(): void
    {
        $vehicle = new Vehicle([]);
        $this->assertFalse($vehicle->isShared());
    }

    // Timestamps

    public function testAutoBoughtTimeInSeconds(): void
    {
        $ts = 1700000000;
        $vehicle = new Vehicle(['autoBoughtTime' => $ts]);
        $this->assertNotNull($vehicle->getAutoBoughtTime());
        $this->assertSame($ts, $vehicle->getAutoBoughtTime()->getTimestamp());
    }

    public function testYunActiveTimeInMilliseconds(): void
    {
        $tsMs = 1700000000000;
        $vehicle = new Vehicle(['yunActiveTime' => $tsMs]);
        $this->assertNotNull($vehicle->getYunActiveTime());
        $this->assertSame(1700000000, $vehicle->getYunActiveTime()->getTimestamp());
    }

    // cfPic fallback

    public function testCfPicFallbackWhenMainUrlsMissing(): void
    {
        $vehicle = new Vehicle([
            'cfPic' => [
                'picMainUrl' => 'https://cdn.example.com/main.jpg',
                'picSetUrl' => 'https://cdn.example.com/set.jpg',
            ],
        ]);

        $this->assertSame('https://cdn.example.com/main.jpg', $vehicle->getPicMainUrl());
        $this->assertSame('https://cdn.example.com/set.jpg', $vehicle->getPicSetUrl());
    }

    public function testCfPicDoesNotOverrideExistingUrls(): void
    {
        $vehicle = new Vehicle([
            'picMainUrl' => 'https://existing.com/main.jpg',
            'picSetUrl' => 'https://existing.com/set.jpg',
            'cfPic' => [
                'picMainUrl' => 'https://cdn.example.com/main.jpg',
                'picSetUrl' => 'https://cdn.example.com/set.jpg',
            ],
        ]);

        $this->assertSame('https://existing.com/main.jpg', $vehicle->getPicMainUrl());
        $this->assertSame('https://existing.com/set.jpg', $vehicle->getPicSetUrl());
    }

    // rangeDetailList

    public function testRangeDetailListEmpty(): void
    {
        $vehicle = new Vehicle(['rangeDetailList' => []]);
        $this->assertSame([], $vehicle->getRangeDetailList());
    }

    public function testRangeDetailListPopulation(): void
    {
        $vehicle = new Vehicle([
            'rangeDetailList' => [
                ['rangeType' => 'EV', 'rangeValue' => '350'],
                ['rangeType' => 'HV', 'rangeValue' => '600'],
            ],
        ]);

        $list = $vehicle->getRangeDetailList();
        $this->assertCount(2, $list);
        $this->assertInstanceOf(EmpowerRange::class, $list[0]);
        $this->assertInstanceOf(EmpowerRange::class, $list[1]);
    }

    // getRaw

    public function testGetRaw(): void
    {
        $data = ['vin' => 'VIN123', 'modelName' => 'BYD SEAL'];
        $vehicle = new Vehicle($data);
        $this->assertSame($data, $vehicle->getRaw());
    }
}
