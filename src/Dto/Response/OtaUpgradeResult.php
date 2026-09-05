<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OtaUpgradeResult
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('status')]
        public ?string $status = null,
        #[SerializedName('message')]
        public ?string $message = null,
        #[SerializedName('statusCode')]
        public ?string $statusCode = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
