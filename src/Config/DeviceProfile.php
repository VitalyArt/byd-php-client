<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

/**
 * Device identity fields sent with every request.
 */
class DeviceProfile
{
    private string $ostype;
    private string $imei;
    private string $mac;
    private string $model;
    private string $sdk;
    private string $mod;
    private string $imeiMd5;
    private string $mobileBrand;
    private string $mobileModel;
    private string $deviceType;
    private string $networkType;
    private string $osType;
    private string $osVersion;

    public function __construct(
        string $ostype = '15',
        string $imei = 'BANGCLE01234',
        string $mac = '00:00:00:00:00:00',
        string $model = 'POCO F1',
        string $sdk = '35',
        string $mod = 'Xiaomi',
        string $imeiMd5 = '00000000000000000000000000000000',
        string $mobileBrand = 'XIAOMI',
        string $mobileModel = 'POCO F1',
        string $deviceType = '0',
        string $networkType = 'wifi',
        string $osType = '15',
        string $osVersion = '35'
    ) {
        $this->ostype = $ostype;
        $this->imei = $imei;
        $this->mac = $mac;
        $this->model = $model;
        $this->sdk = $sdk;
        $this->mod = $mod;
        $this->imeiMd5 = $imeiMd5;
        $this->mobileBrand = $mobileBrand;
        $this->mobileModel = $mobileModel;
        $this->deviceType = $deviceType;
        $this->networkType = $networkType;
        $this->osType = $osType;
        $this->osVersion = $osVersion;
    }

    // Getters
    public function getOstype(): string
    {
        return $this->ostype;
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
