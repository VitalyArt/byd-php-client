<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

use function is_array;

/**
 * A vehicle associated with the user's account.
 */
class Vehicle extends BaseModel
{
    private string $vin = '';

    private string $modelName = '';

    private string $brandName = '';

    private string $energyType = '';

    private string $autoAlias = '';

    private string $autoPlate = '';

    private string $picMainUrl = '';

    private string $picSetUrl = '';

    private string $outModelType = '';

    private ?float $totalMileage = null;

    private ?int $modelId = null;

    private ?int $carType = null;

    private bool $defaultCar = false;

    private ?int $empowerType = null;

    private ?int $permissionStatus = null;

    private string $tboxVersion = '';

    private string $vehicleState = '';

    private ?DateTimeInterface $autoBoughtTime = null;

    private ?DateTimeInterface $yunActiveTime = null;

    private ?int $empowerId = null;

    /**
     * @var EmpowerRange[]
     */
    private array $rangeDetailList = [];

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->vin = (string) ($data['vin'] ?? '');
        $this->modelName = (string) ($data['modelName'] ?? '');
        $this->brandName = (string) ($data['brandName'] ?? '');
        $this->energyType = (string) ($data['energyType'] ?? '');
        $this->autoAlias = (string) ($data['autoAlias'] ?? '');
        $this->autoPlate = (string) ($data['autoPlate'] ?? '');
        $this->picMainUrl = (string) ($data['picMainUrl'] ?? '');
        $this->picSetUrl = (string) ($data['picSetUrl'] ?? '');
        $this->outModelType = (string) ($data['outModelType'] ?? '');
        $this->totalMileage = isset($data['totalMileage']) ? (float) $data['totalMileage'] : null;
        $this->modelId = isset($data['modelId']) ? (int) $data['modelId'] : null;
        $this->carType = isset($data['carType']) ? (int) $data['carType'] : null;
        $this->defaultCar = (bool) ($data['defaultCar'] ?? false);
        $this->empowerType = isset($data['empowerType']) ? (int) $data['empowerType'] : null;
        $this->permissionStatus = isset($data['permissionStatus']) ? (int) $data['permissionStatus'] : null;
        $this->tboxVersion = (string) ($data['tboxVersion'] ?? '');
        $this->vehicleState = (string) ($data['vehicleState'] ?? '');

        // Handle timestamps
        if (isset($data['autoBoughtTime'])) {
            $this->autoBoughtTime = $this->parseTimestamp($data['autoBoughtTime']);
        }

        if (isset($data['yunActiveTime'])) {
            $this->yunActiveTime = $this->parseTimestamp($data['yunActiveTime']);
        }

        $this->empowerId = isset($data['empowerId']) ? (int) $data['empowerId'] : null;

        // Handle range detail list
        $this->rangeDetailList = [];
        if (isset($data['rangeDetailList']) && is_array($data['rangeDetailList'])) {
            foreach ($data['rangeDetailList'] as $item) {
                $this->rangeDetailList[] = new EmpowerRange($item);
            }
        }

        // Handle cfPic if main URLs are missing
        if ((!$this->picMainUrl || !$this->picSetUrl) && isset($data['cfPic']) && is_array($data['cfPic'])) {
            $cfPic = $data['cfPic'];
            $this->picMainUrl = $this->picMainUrl ?: (string) ($cfPic['picMainUrl'] ?? $cfPic['pic_main_url'] ?? '');
            $this->picSetUrl = $this->picSetUrl ?: (string) ($cfPic['picSetUrl'] ?? $cfPic['pic_set_url'] ?? '');
        }
    }

    private function parseTimestamp($timestamp): ?DateTimeInterface
    {
        if ($timestamp === null) {
            return null;
        }

        $ts = (int) $timestamp;
        // Threshold to distinguish seconds from milliseconds.
        if ($ts >= 1000000000000) {
            $ts = (int) ($ts / 1000);
        }

        return DateTimeImmutable::createFromFormat('U', (string) $ts);
    }

    public function isShared(): bool
    {
        return $this->empowerType !== null && $this->empowerType < 0;
    }

    // Getters
    public function getVin(): string
    {
        return $this->vin;
    }

    public function getModelName(): string
    {
        return $this->modelName;
    }

    public function getBrandName(): string
    {
        return $this->brandName;
    }

    public function getEnergyType(): string
    {
        return $this->energyType;
    }

    public function getAutoAlias(): string
    {
        return $this->autoAlias;
    }

    public function getAutoPlate(): string
    {
        return $this->autoPlate;
    }

    public function getPicMainUrl(): string
    {
        return $this->picMainUrl;
    }

    public function getPicSetUrl(): string
    {
        return $this->picSetUrl;
    }

    public function getOutModelType(): string
    {
        return $this->outModelType;
    }

    public function getTotalMileage(): ?float
    {
        return $this->totalMileage;
    }

    public function getModelId(): ?int
    {
        return $this->modelId;
    }

    public function getCarType(): ?int
    {
        return $this->carType;
    }

    public function isDefaultCar(): bool
    {
        return $this->defaultCar;
    }

    public function getEmpowerType(): ?int
    {
        return $this->empowerType;
    }

    public function getPermissionStatus(): ?int
    {
        return $this->permissionStatus;
    }

    public function getTboxVersion(): string
    {
        return $this->tboxVersion;
    }

    public function getVehicleState(): string
    {
        return $this->vehicleState;
    }

    public function getAutoBoughtTime(): ?DateTimeInterface
    {
        return $this->autoBoughtTime;
    }

    public function getYunActiveTime(): ?DateTimeInterface
    {
        return $this->yunActiveTime;
    }

    public function getEmpowerId(): ?int
    {
        return $this->empowerId;
    }

    /**
     * @return EmpowerRange[]
     */
    public function getRangeDetailList(): array
    {
        return $this->rangeDetailList;
    }
}
