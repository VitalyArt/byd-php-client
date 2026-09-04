<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchBluetoothInfo
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('dkey')]
        #[SensitiveParameter]
        public ?string $digitalKey = null,
        #[SerializedName('keyNumber')]
        public int $keyNumber = 0,
        #[SerializedName('keyValidTo')]
        public ?int $validUntil = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
