<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\HvacStatus;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class HvacApi
{
    private const ENDPOINT = '/control/getStatusNow';

    /**
     * Fetch current HVAC/climate control status for a vehicle.
     */
    public static function fetchHvacStatus(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin
    ): HvacStatus {
        $inner = Common::buildInnerBase($config, null, $vin);

        $decoded = TokenJson::postTokenJson(
            self::ENDPOINT,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        return new HvacStatus(is_array($decoded) ? $decoded : []);
    }
}
