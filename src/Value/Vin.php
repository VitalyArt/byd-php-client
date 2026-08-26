<?php

declare(strict_types=1);

namespace Byd\ApiClient\Value;

use Byd\ApiClient\Exception\ValidationException;
use Stringable;

final readonly class Vin implements Stringable
{
    public string $value;

    public function __construct(string $value)
    {
        $value = strtoupper(trim($value));
        if (preg_match('/^[A-HJ-NPR-Z0-9]{17}$/', $value) !== 1) {
            throw new ValidationException('VIN must contain 17 valid ISO 3779 characters.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
