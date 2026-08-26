<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use SensitiveParameter;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ApiEnvelopeRequest
{
    public function __construct(
        #[SerializedName('countryCode')]
        public string $countryCode,
        #[SerializedName('encryData')]
        #[SensitiveParameter]
        public string $encryptedData,
        #[SerializedName('identifier')]
        public string $identifier,
        #[SerializedName('imeiMD5')]
        public string $imeiHash,
        #[SerializedName('language')]
        public string $language,
        #[SerializedName('reqTimestamp')]
        public string $requestTimestamp,
        #[SerializedName('sign')]
        #[SensitiveParameter]
        public string $signature,
        #[SerializedName('ostype')]
        public string $osType,
        #[SerializedName('imei')]
        public string $imei,
        #[SerializedName('mac')]
        public string $mac,
        #[SerializedName('model')]
        public string $model,
        #[SerializedName('sdk')]
        public string $sdk,
        #[SerializedName('mod')]
        public string $manufacturer,
        #[SerializedName('serviceTime')]
        public string $serviceTime,
        #[SerializedName('checkcode')]
        public string $checkcode = '',
    ) {
    }
}
