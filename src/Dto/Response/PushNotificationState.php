<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class PushNotificationState
{
    /**
     * @param list<PushSwitch> $switches
     * @param array<array-key, mixed> $raw
     */
    public function __construct(
        #[SerializedName('switches')]
        public array $switches,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isEnabled(int $type = PushSwitch::VEHICLE_STATUS_TYPE): ?bool
    {
        foreach ($this->switches as $switch) {
            if ($switch->type === $type) {
                return $switch->isEnabled();
            }
        }

        return null;
    }
}
