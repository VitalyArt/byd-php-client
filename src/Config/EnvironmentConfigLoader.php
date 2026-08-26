<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Exception\ValidationException;

use function is_string;

final class EnvironmentConfigLoader
{
    /** @param array<string, string|false> $environment */
    public function load(array $environment): ClientConfig
    {
        $username = $environment['BYD_USERNAME'] ?? false;
        $password = $environment['BYD_PASSWORD'] ?? false;
        if (!is_string($username) || !is_string($password)) {
            throw new ValidationException('BYD_USERNAME and BYD_PASSWORD are required.');
        }

        return new ClientConfig(
            credentials: new Credentials($username, $password, $this->nullable($environment['BYD_CONTROL_PIN'] ?? false)),
            locale: new Locale(
                countryCode: $this->value($environment, 'BYD_COUNTRY_CODE', 'NL'),
                language: $this->value($environment, 'BYD_LANGUAGE', 'en'),
                timeZone: $this->value($environment, 'BYD_TIME_ZONE', 'Europe/Amsterdam'),
            ),
            protocol: new ProtocolOptions(baseUrl: $this->value($environment, 'BYD_BASE_URL', 'https://dilinkappoversea-eu.byd.auto')),
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
