<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ChargingSchedule
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('startChargeTime')]
        public ?string $startTime = null,
        #[SerializedName('endChargeTime')]
        public ?string $endTime = null,
        #[SerializedName('status')]
        public string|int|null $status = null,
        #[SerializedName('chargeWay')]
        public ?string $chargeWay = null,
        #[SerializedName('exeTime')]
        public ?string $executionTime = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isEnabled(): ?bool
    {
        return match ((string) $this->status) {
            '0' => false,
            '1' => true,
            default => null,
        };
    }
}
