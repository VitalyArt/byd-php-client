<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchTokenInfo
{
    public function __construct(
        #[SerializedName('accountStatus')]
        public int $accountStatus,
        #[SerializedName('userId')]
        public string $userId,
        #[SerializedName('watchImei')]
        public string $watchImei,
        #[SerializedName('encryToken')]
        #[SensitiveParameter]
        public string $encryptionToken,
        #[SerializedName('signToken')]
        #[SensitiveParameter]
        public string $signToken,
        #[SerializedName('timeStamp')]
        public int $timestamp,
        #[SerializedName('language')]
        public string $language,
        #[SerializedName('vin')]
        public string $vin,
        #[SerializedName('userType')]
        public string $userType,
    ) {
    }
}
