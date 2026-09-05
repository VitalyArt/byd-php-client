<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\BydClient;
use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Config\EnvironmentConfigLoader;
use Byd\ApiClient\Enum\ApiRegion;
use Byd\ApiClient\Enum\CountryCode;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class ConfigurationTest extends TestCase
{
    public function testEnvironmentLoaderDerivesBaseUrlFromCountryCode(): void
    {
        $client = new EnvironmentConfigLoader()->load([
            'BYD_USERNAME' => 'user@example.com',
            'BYD_PASSWORD' => 'password',
            'BYD_COUNTRY_CODE' => 'uz',
        ]);

        $config = $this->config($client);

        self::assertSame(CountryCode::UZ, $config->locale->countryCode);
        self::assertSame(ApiRegion::UZBEKISTAN->value, $config->protocol->baseUrl);
    }

    public function testClientCanBeConfiguredDirectly(): void
    {
        $client = new BydClient(
            username: 'user@example.com',
            password: 'password',
            countryCode: CountryCode::UZ,
            language: 'ru',
            timeZone: 'Asia/Tashkent',
        );
        $config = $this->config($client);

        self::assertInstanceOf(ClientConfig::class, $config);
        self::assertSame(CountryCode::UZ, $config->locale->countryCode);
        self::assertSame(ApiRegion::UZBEKISTAN->value, $config->protocol->baseUrl);
    }

    private function config(BydClient $client): ClientConfig
    {
        $property = new ReflectionProperty(BydClient::class, 'config');
        $config = $property->getValue($client);

        self::assertInstanceOf(ClientConfig::class, $config);

        return $config;
    }
}
