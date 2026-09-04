<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchVehicleConfiguration
{
    /**
     * @param array<string, mixed> $configuration
     * @param array<string, mixed> $raw
     */
    public function __construct(
        #[SerializedName('modelNameOut')]
        public ?string $modelName = null,
        #[SerializedName('cfVechicle')]
        public array $configuration = [],
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
