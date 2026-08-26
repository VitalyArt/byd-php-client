<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

/**
 * Device identity fields sent with every request.
 */
class DeviceProfile
{
    public function __construct(
        // `ostype` is the outer device identifier; the inner login field
        // `osType` is separately sent as the Android OS version (`15`).
        private string $osType = 'and',
        private string $imei = 'BANGCLE01234',
        private string $mac = '00:00:00:00:00:00',
        private string $model = 'POCO F1',
        private string $sdk = '35',
        private string $mod = 'Xiaomi',
        private string $imeiMd5 = '00000000000000000000000000000000',
        private string $mobileBrand = 'XIAOMI',
        private string $mobileModel = 'POCO F1',
        private string $deviceType = '0',
        private string $networkType = 'wifi',
        private string $osVersion = '35'
    ) {
    }

    // Getters
    public function getOstype(): string
    {
        return $this->osType;
    }

    public function getImei(): string
    {
        return $this->imei;
    }

    public function getMac(): string
    {
        return $this->mac;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getSdk(): string
    {
        return $this->sdk;
    }

    public function getMod(): string
    {
        return $this->mod;
    }

    public function getImeiMd5(): string
    {
        return $this->imeiMd5;
    }

    public function getMobileBrand(): string
    {
        return $this->mobileBrand;
    }

    public function getMobileModel(): string
    {
        return $this->mobileModel;
    }

    public function getDeviceType(): string
    {
        return $this->deviceType;
    }

    public function getNetworkType(): string
    {
        return $this->networkType;
    }

    public function getOsVersion(): string
    {
        return $this->osVersion;
    }
}
