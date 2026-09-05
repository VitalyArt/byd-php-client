<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\BydClient;
use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Exception\ValidationException;

use function is_string;

final class EnvironmentConfigLoader
{
    /** @param array<string, string|false> $environment */
    public function load(array $environment): BydClient
    {
        $username = $environment['BYD_USERNAME'] ?? false;
        $password = $environment['BYD_PASSWORD'] ?? false;
        if (!is_string($username) || !is_string($password)) {
            throw new ValidationException('BYD_USERNAME and BYD_PASSWORD are required.');
        }

        $countryCode = CountryCode::parse($this->value($environment, 'BYD_COUNTRY_CODE', CountryCode::NL->value));
        if (!$countryCode instanceof CountryCode) {
            throw new ValidationException('BYD_COUNTRY_CODE must be a supported ISO 3166-1 alpha-2 code.');
        }

        return new BydClient(
            username: $username,
            password: $password,
            countryCode: $countryCode,
            language: $this->value($environment, 'BYD_LANGUAGE', 'en'),
            timeZone: $this->value($environment, 'BYD_TIME_ZONE', 'Europe/Amsterdam'),
            controlPin: $this->nullable($environment['BYD_CONTROL_PIN'] ?? false),
        );
    }

    /** @param array<string, string|false> $environment */
    private function value(array $environment, string $key, string $default): string
    {
        $value = $environment[$key] ?? false;

        return is_string($value) && $value !== '' ? $value : $default;
    }

    private function nullable(string|false $value): ?string
    {
        return is_string($value) && $value !== '' ? $value : null;
    }
}
