<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Protocol;

use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class LoginInnerRequest
{
    public function __construct(
        #[SerializedName('agreeStatus')]
        public string $agreementStatus,
        #[SerializedName('agreementType')]
        public string $agreementType,
        #[SerializedName('appInnerVersion')]
        public string $appInnerVersion,
        #[SerializedName('appVersion')]
        public string $appVersion,
        #[SerializedName('deviceName')]
        public string $deviceName,
        #[SerializedName('deviceType')]
        public string $deviceType,
        #[SerializedName('imeiMD5')]
        public string $imeiHash,
        #[SerializedName('isAuto')]
        public string $isAutomatic,
        #[SerializedName('mobileBrand')]
        public string $mobileBrand,
        #[SerializedName('mobileModel')]
        public string $mobileModel,
        #[SerializedName('networkType')]
        public string $networkType,
        #[SerializedName('osType')]
        public string $osType,
        #[SerializedName('osVersion')]
        public string $osVersion,
        #[SerializedName('random')]
        public string $nonce,
        #[SerializedName('softType')]
        public string $softType,
        #[SerializedName('timeStamp')]
        public string $timestamp,
        #[SerializedName('timeZone')]
        public string $timeZone,
    ) {
    }
}
