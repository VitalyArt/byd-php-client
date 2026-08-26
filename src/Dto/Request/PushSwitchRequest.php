<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PushSwitchRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('type')]
        public int $type,
        #[SerializedName('state')]
        public string $state,
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->state === '1';
    }
}
