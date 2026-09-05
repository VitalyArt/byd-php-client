<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\OtaUpgradeStatus;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class OtaUpdateInfo
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('bookingTimeStamp')]
        public ?int $bookingTimestamp = null,
        #[SerializedName('currentVersion')]
        public string $currentVersion = '',
        #[SerializedName('updateVersion')]
        public string $updateVersion = '',
        #[SerializedName('estimateUpgradeTime')]
        public ?int $estimatedUpgradeTime = null,
        #[SerializedName('padTimeZone')]
        public string $vehicleTimeZone = '',
        #[SerializedName('functionDescription')]
        public string $description = '',
        #[SerializedName('upgradeStatus')]
        public OtaUpgradeStatus $status = OtaUpgradeStatus::UNKNOWN,
        #[SerializedName('functionAddition')]
        public string $addedFeatures = '',
        #[SerializedName('functionOptimization')]
        public string $optimizations = '',
        #[SerializedName('acknowledgements')]
        public string $acknowledgements = '',
        #[SerializedName('currentUpdateTime')]
        public ?int $currentUpdateTimestamp = null,
        #[SerializedName('upgradeResult')]
        public ?OtaUpgradeResult $upgradeResult = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function hasUpdate(): bool
    {
        return $this->updateVersion !== '' && $this->updateVersion !== $this->currentVersion;
    }
}
