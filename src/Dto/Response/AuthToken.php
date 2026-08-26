<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class AuthToken
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('userId')]
        public string $userId,
        #[SerializedName('signToken')]
        #[SensitiveParameter]
        public string $signToken,
        #[SerializedName('encryToken')]
        #[SensitiveParameter]
        public string $encryptionToken,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
