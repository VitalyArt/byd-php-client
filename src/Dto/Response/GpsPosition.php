<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class GpsPosition
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('longitude')]
        public ?float $longitude = null,
        #[SerializedName('latitude')]
        public ?float $latitude = null,
        #[SerializedName('altitude')]
        public ?float $altitude = null,
        #[SerializedName('speed')]
        public ?float $speed = null,
        #[SerializedName('heading')]
        public ?float $heading = null,
        #[SerializedName('direction')]
        public ?float $direction = null,
        #[SerializedName('positionType')]
        public ?string $positionType = null,
        #[SerializedName('timestamp')]
        public string|int|null $timestamp = null,
        #[SerializedName('requestSerial')]
        public ?string $requestSerial = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
