<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Exception\ValidationException;
use DateTimeZone;
use Exception;

final readonly class Locale
{
    public function __construct(
        public CountryCode $countryCode = CountryCode::NL,
        public string $language = 'en',
        public string $timeZone = 'Europe/Amsterdam',
    ) {
        if (preg_match('/^[a-z]{2}(?:-[A-Z]{2})?$/', $language) !== 1) {
            throw new ValidationException('Language must be a supported locale identifier.');
        }

        try {
            new DateTimeZone($timeZone);
        } catch (Exception $exception) {
            throw new ValidationException('Invalid time zone.', $exception->getCode(), previous: $exception);
        }
    }
}
