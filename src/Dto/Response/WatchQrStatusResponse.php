<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\WatchQrStatus;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchQrStatusResponse
{
    public function __construct(
        #[SerializedName('status')]
        public WatchQrStatus $status,
    ) {
    }
}
