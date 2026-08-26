<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use function in_array;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PushSwitch
{
    public const int VEHICLE_STATUS_TYPE = 701;

    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('type')]
        public int $type,
        #[SerializedName('state')]
        public string|int|bool $state,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isEnabled(): bool
    {
        return in_array($this->state, [true, 1, '1'], true);
    }
}
