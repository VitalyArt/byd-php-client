<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\EnergyConsumption;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class EnergyApi
{
    private const ENDPOINT = '/vehicleInfo/vehicle/getEnergyConsumption';

    /**
     * Fetch energy consumption data for a vehicle.
     */
    public static function fetchEnergyConsumption(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        string $powerType = '0',
        ?string $autoModelNameOut = null
    ): EnergyConsumption {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner['powerType'] = $powerType;
        $inner['requestType'] = 0;
        if ($autoModelNameOut !== null && $autoModelNameOut !== '') {
            $inner['autoModelNameOut'] = $autoModelNameOut;
        }

        $decoded = TokenJson::postTokenJson(
            self::ENDPOINT,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        return new EnergyConsumption(is_array($decoded) ? $decoded : []);
    }
}
