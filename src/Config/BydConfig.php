<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use function is_array;

/**
 * Client configuration.
 */
class BydConfig
{
    private string $username;
    private string $password;
    private string $baseUrl;
    private string $countryCode;
    private string $language;
    private string $timeZone;
    private string $appVersion;
    private string $appInnerVersion;
    private string $softType;
    private string $tboxVersion;
    private string $isAuto;
    private ?string $controlPin;
    private float $sessionTtl;
    private bool $mqttEnabled;
    private int $mqttKeepalive;
    private float $mqttTimeout;
    private DeviceProfile $device;

    public function __construct(
        string $username,
        string $password,
        string $baseUrl = 'https://dilinkappoversea-eu.byd.auto',
        string $countryCode = 'NL',
        string $language = 'en',
        string $timeZone = 'Europe/Amsterdam',
        string $appVersion = '3.2.2',
        string $appInnerVersion = '322',
        string $softType = '0',
        string $tboxVersion = '3',
        string $isAuto = '1',
        ?string $controlPin = null,
        float $sessionTtl = 43200.0, // 12 hours
        bool $mqttEnabled = true,
        int $mqttKeepalive = 120,
        float $mqttTimeout = 10.0,
        ?DeviceProfile $device = null
    ) {
        $this->username = $username;
        $this->password = $password;
        $this->baseUrl = $baseUrl;
        $this->countryCode = $countryCode;
        $this->language = $language;
        $this->timeZone = $timeZone;
        $this->appVersion = $appVersion;
        $this->appInnerVersion = $appInnerVersion;
        $this->softType = $softType;
        $this->tboxVersion = $tboxVersion;
        $this->isAuto = $isAuto;
        $this->controlPin = $controlPin;
        $this->sessionTtl = $sessionTtl;
        $this->mqttEnabled = $mqttEnabled;
        $this->mqttKeepalive = $mqttKeepalive;
        $this->mqttTimeout = $mqttTimeout;
        $this->device = $device ?? new DeviceProfile();
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
                $deviceOverrides['ostype'] ?? '15',
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
                $deviceOverrides['os_type'] ?? '15',
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
