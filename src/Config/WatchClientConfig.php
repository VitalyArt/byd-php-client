<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Enum\ApiRegion;
use Byd\ApiClient\Policy\PollingPolicy;

final readonly class WatchClientConfig
{
    public function __construct(
        public WatchDeviceProfile $device,
        public Locale $locale = new Locale(),
        public ?string $baseUrl = null,
        public PollingPolicy $polling = new PollingPolicy(51, 3000, 150000),
    ) {
    }

    public function resolvedBaseUrl(): string
    {
        if ($this->baseUrl !== null) {
            return rtrim($this->baseUrl, '/');
        }

        return ApiRegion::forCountry($this->locale->countryCode)->value;
    }
}
