<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Enum\ApiRegion;
use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Exception\ValidationException;

final readonly class ProtocolOptions
{
    public static function forCountry(CountryCode $countryCode): self
    {
        return self::forRegion(ApiRegion::forCountry($countryCode));
    }

    public static function forRegion(ApiRegion $region): self
    {
        return new self(baseUrl: $region->value);
    }

    public function __construct(
        public string $baseUrl = ApiRegion::EUROPE->value,
        public string $appName = 'pyBYD+0.0.73',
        public string $appVersion = '3.5.0',
        public string $appInnerVersion = '352',
        public string $softType = '0',
        public string $tboxVersion = '3',
        public bool $automaticLogin = true,
    ) {
        if (filter_var($baseUrl, FILTER_VALIDATE_URL) === false || parse_url($baseUrl, PHP_URL_SCHEME) !== 'https') {
            throw new ValidationException('BYD base URL must be a valid HTTPS URL.');
        }
    }
}
