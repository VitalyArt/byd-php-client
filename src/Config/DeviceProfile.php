<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

final readonly class DeviceProfile
{
    public function __construct(
        public string $osType = 'and',
        public string $imei = 'BANGCLE01234',
        public string $mac = '00:00:00:00:00:00',
        public string $model = 'POCO F1',
        public string $sdk = '35',
        public string $manufacturer = 'Xiaomi',
        public string $mobileBrand = 'XIAOMI',
        public string $mobileModel = 'POCO F1',
        public string $deviceType = '0',
        public string $networkType = 'wifi',
        public string $osVersion = '35',
    ) {
    }
}
