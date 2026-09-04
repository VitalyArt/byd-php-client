<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\WatchQrStatus;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchQrSession
{
    public function __construct(
        #[SerializedName('watchImei')]
        public string $watchImei,
        #[SerializedName('uuid')]
        public string $uuid,
        #[SerializedName('status')]
        public WatchQrStatus $status,
        #[Ignore]
        public string $qrPayload,
        #[Ignore]
        public int $createdAtMilliseconds,
    ) {
    }

    public function expiresAtMilliseconds(): int
    {
        return $this->createdAtMilliseconds + 150000;
    }
}
