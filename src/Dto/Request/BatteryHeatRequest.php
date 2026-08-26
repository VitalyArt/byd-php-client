<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class BatteryHeatRequest
{
    public function __construct(#[SerializedName('batteryHeat')] public bool $enabled)
    {
    }
}
