<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchCommonRequest
{
    public function __construct(
        #[SerializedName('identifier')]
        public string $identifier,
        #[SerializedName('watchImei')]
        public string $watchImei,
        #[SerializedName('watchModel')]
        public string $watchModel,
        #[SerializedName('watchName')]
        public string $watchName,
        #[SerializedName('watchBrand')]
        public string $watchBrand,
        #[SerializedName('watchAppVersion')]
        public string $watchAppVersion,
        #[SerializedName('watchOs')]
        public string $watchOs,
        #[SerializedName('reqTimestamp')]
        public string $requestTimestamp,
        #[SerializedName('language')]
        public string $language,
        #[SerializedName('countryCode')]
        public string $countryCode,
        #[SerializedName('userType')]
        public string $userType,
        #[SerializedName('encryData')]
        #[SensitiveParameter]
        public string $encryptedData,
        #[SerializedName('sign')]
        #[SensitiveParameter]
        public string $sign,
    ) {
    }
}
