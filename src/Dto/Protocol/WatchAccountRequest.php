<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class WatchAccountRequest
{
    public function __construct(
        #[SerializedName('identifier')]
        public string $identifier,
        #[SerializedName('watchImei')]
        public string $watchImei,
        #[SerializedName('watchModel')]
        public string $watchModel,
        #[SerializedName('sign')]
        #[SensitiveParameter]
        public string $sign,
        #[SerializedName('watchBrand')]
        public string $watchBrand,
        #[SerializedName('reqTimestamp')]
        public string $requestTimestamp,
        #[SerializedName('watchName')]
        public string $watchName,
        #[SerializedName('watchAppVersion')]
        public string $watchAppVersion,
        #[SerializedName('watchOs')]
        public string $watchOs,
        #[SerializedName('language')]
        public string $language,
        #[SerializedName('countryCode')]
        public string $countryCode,
        #[SerializedName('encryData')]
        #[SensitiveParameter]
        public string $encryptedData,
    ) {
    }
}
