<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchTokenResponse
{
    public function __construct(
        #[SerializedName('watchTokenInfo')]
        public WatchTokenInfo $token,
        #[SerializedName('controlPwd')]
        #[SensitiveParameter]
        public ?string $controlPassword = null,
    ) {
    }
}
