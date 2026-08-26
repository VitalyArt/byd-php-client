<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum PowerType: string
{
    case ELECTRIC = '0';
    case HYBRID = '2';

    public static function fromVehicleLabel(string $label): self
    {
        return EnergyType::fromVehicleLabel($label) === EnergyType::HYBRID ? self::HYBRID : self::ELECTRIC;
    }
}
