<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

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

        return match ($this->locale->countryCode) {
            'SG' => 'https://dilinkappoversea-sg.byd.auto',
            'AU' => 'https://dilinkappoversea-au.byd.auto',
            'BR' => 'https://dilinkappoversea-br.byd.auto',
            'JP' => 'https://dilinkappoversea-jp.byd.auto',
            'UZ' => 'https://dilinkappoversea-uz.byd.auto',
            'NO' => 'https://dilinkappoversea-no.byd.auto',
            'MX' => 'https://dilinkappoversea-mx.byd.auto',
            'KR' => 'https://dilinkappoversea-kr-ali.byd.auto',
            'ID' => 'https://dilinkappoversea-id.byd.auto',
            'TR' => 'https://dilinkappoversea-tr.byd.auto',
            'OM' => 'https://dilinkappoversea-om.byd.auto',
            'IN' => 'https://dilinkappoversea-in.byd.auto',
            'VN' => 'https://dilinkappoversea-vn.byd.auto',
            'SA' => 'https://dilinkappoversea-sa.byd.auto',
            'KZ' => 'https://dilinkappoversea-kz.byd.auto',
            default => 'https://dilinkappoversea-eu.byd.auto',
        };
    }
}
