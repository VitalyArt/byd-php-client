<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum EnergyType: string
{
    case ELECTRIC = '0';
    case HYBRID = '1';

    public static function fromVehicleLabel(string $label): self
    {
        return match (strtoupper($label)) {
            '1', 'PHEV', 'HYBRID', 'HEV' => self::HYBRID,
            default => self::ELECTRIC,
        };
    }
}
