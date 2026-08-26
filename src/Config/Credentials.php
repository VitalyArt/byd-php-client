<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Exception\ValidationException;
use SensitiveParameter;

final readonly class Credentials
{
    public function __construct(
        public string $username,
        #[SensitiveParameter]
        public string $password,
        #[SensitiveParameter]
        public ?string $controlPin = null,
    ) {
        if (trim($username) === '' || $password === '') {
            throw new ValidationException('BYD username and password must not be empty.');
        }

        if ($controlPin !== null && preg_match('/^\d{4,8}$/', $controlPin) !== 1) {
            throw new ValidationException('Control PIN must contain between 4 and 8 digits.');
        }
    }
}
