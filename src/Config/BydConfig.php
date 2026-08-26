<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Crypto\Hashing;

use function is_array;

/**
 * Client configuration.
 */
class BydConfig
{
    public function __construct(
        private string $username,
        private string $password,
        private string $baseUrl = 'https://dilinkappoversea-eu.byd.auto',
        private string $countryCode = 'NL',
        private string $language = 'en',
        private string $timeZone = 'Europe/Amsterdam',
        private string $appVersion = '3.2.2',
        private string $appInnerVersion = '322',
        private string $softType = '0',
        private string $tboxVersion = '3',
        private string $isAuto = '1',
        private ?string $controlPin = null,
        private float $sessionTtl = 43200.0,
        // 12 hours
        private bool $mqttEnabled = true,
        private int $mqttKeepalive = 120,
        private float $mqttTimeout = 10.0,
        private ?DeviceProfile $device = new DeviceProfile()
    ) {
        // Keep the PHP defaults in sync with pyBYD. The server expects the
        // account-derived IMEI hash, not the all-zero placeholder.
        $this->device ??= new DeviceProfile();
        if ($this->device->getImeiMd5() === '00000000000000000000000000000000') {
            $this->device = new DeviceProfile(
                $this->device->getOstype(),
                $this->device->getImei(),
                $this->device->getMac(),
                $this->device->getModel(),
                $this->device->getSdk(),
                $this->device->getMod(),
                Hashing::md5Hex($this->username),
                $this->device->getMobileBrand(),
                $this->device->getMobileModel(),
                $this->device->getDeviceType(),
                $this->device->getNetworkType(),
                $this->device->getOsVersion()
            );
        }
    }

    public static function fromEnv(array $overrides = []): self
    {
        $env = $_ENV;

        $deviceOverrides = $overrides['device'] ?? null;
        unset($overrides['device']);

        $device = null;
        if ($deviceOverrides instanceof DeviceProfile) {
            $device = $deviceOverrides;
        } elseif (is_array($deviceOverrides)) {
            $device = new DeviceProfile(
                $deviceOverrides['ostype'] ?? 'and',
                $deviceOverrides['imei'] ?? 'BANGCLE01234',
                $deviceOverrides['mac'] ?? '00:00:00:00:00:00',
                $deviceOverrides['model'] ?? 'POCO F1',
                $deviceOverrides['sdk'] ?? '35',
                $deviceOverrides['mod'] ?? 'Xiaomi',
                $deviceOverrides['imei_md5'] ?? '00000000000000000000000000000000',
                $deviceOverrides['mobile_brand'] ?? 'XIAOMI',
                $deviceOverrides['mobile_model'] ?? 'POCO F1',
                $deviceOverrides['device_type'] ?? '0',
                $deviceOverrides['network_type'] ?? 'wifi',
                $deviceOverrides['os_version'] ?? '35'
            );
        }

        $config = [
            'username' => $env['BYD_USERNAME'] ?? '',
            'password' => $env['BYD_PASSWORD'] ?? '',
            'baseUrl' => $env['BYD_BASE_URL'] ?? 'https://dilinkappoversea-eu.byd.auto',
            'countryCode' => $env['BYD_COUNTRY_CODE'] ?? 'NL',
            'language' => $env['BYD_LANGUAGE'] ?? 'en',
            'timeZone' => $env['BYD_TIME_ZONE'] ?? 'Europe/Amsterdam',
            'appVersion' => $env['BYD_APP_VERSION'] ?? '3.2.2',
            'appInnerVersion' => $env['BYD_APP_INNER_VERSION'] ?? '322',
            'softType' => $env['BYD_SOFT_TYPE'] ?? '0',
            'tboxVersion' => $env['BYD_TBOX_VERSION'] ?? '3',
            'isAuto' => $env['BYD_IS_AUTO'] ?? '1',
            'controlPin' => $env['BYD_CONTROL_PIN'] ?? null,
            'sessionTtl' => (float) ($env['BYD_SESSION_TTL'] ?? 43200.0),
            'mqttEnabled' => filter_var($env['BYD_MQTT_ENABLED'] ?? true, FILTER_VALIDATE_BOOLEAN),
            'mqttKeepalive' => (int) ($env['BYD_MQTT_KEEPALIVE'] ?? 120),
            'mqttTimeout' => (float) ($env['BYD_MQTT_TIMEOUT'] ?? 10.0),
            'device' => $device,
        ];

        // Apply overrides
        foreach ($overrides as $key => $value) {
            $config[$key] = $value;
        }

        return new self(
            $config['username'],
            $config['password'],
            $config['baseUrl'],
            $config['countryCode'],
            $config['language'],
            $config['timeZone'],
            $config['appVersion'],
            $config['appInnerVersion'],
            $config['softType'],
            $config['tboxVersion'],
            $config['isAuto'],
            $config['controlPin'],
            $config['sessionTtl'],
            $config['mqttEnabled'],
            $config['mqttKeepalive'],
            $config['mqttTimeout'],
            $config['device']
        );
    }

    // Getters
    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTimeZone(): string
    {
        return $this->timeZone;
    }

    public function getAppVersion(): string
    {
        return $this->appVersion;
    }

    public function getAppInnerVersion(): string
    {
        return $this->appInnerVersion;
    }

    public function getSoftType(): string
    {
        return $this->softType;
    }

    public function getTboxVersion(): string
    {
        return $this->tboxVersion;
    }

    public function getIsAuto(): string
    {
        return $this->isAuto;
    }

    public function getControlPin(): ?string
    {
        return $this->controlPin;
    }

    public function getSessionTtl(): float
    {
        return $this->sessionTtl;
    }

    public function isMqttEnabled(): bool
    {
        return $this->mqttEnabled;
    }

    public function getMqttKeepalive(): int
    {
        return $this->mqttKeepalive;
    }

    public function getMqttTimeout(): float
    {
        return $this->mqttTimeout;
    }

    public function getDevice(): DeviceProfile
    {
        return $this->device;
    }
}
