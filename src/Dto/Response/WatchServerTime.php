<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchServerTime
{
    public function __construct(
        #[SerializedName('serverTime')]
        public int $serverTime,
        #[SerializedName('timeSpan')]
        public string $timeSpan = '',
    ) {
    }
}
