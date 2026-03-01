<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\Vehicle;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class VehicleApi
{
    /**
     * Fetch all vehicles associated with the authenticated user.
     *
     * @return Vehicle[]
     */
    public static function fetchVehicleList(
        BydConfig $config,
        Session $session,
        TransportInterface $transport
    ): array {
        $inner = Common::buildInnerBase($config);
        $decoded = TokenJson::postTokenJson(
            '/app/account/getAllListByUserId',
            $config,
            $session,
            $transport,
            $inner
        );

        $items = is_array($decoded) ? $decoded : [];
        $vehicles = [];

        foreach ($items as $item) {
            if (is_array($item)) {
                $vehicles[] = new Vehicle($item);
            }
        }

        return $vehicles;
    }
}
