<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\CommandAck;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class VehicleSettingsApi
{
    private const RENAME_ENDPOINT = '/control/vehicle/modifyAutoAlias';

    /**
     * Rename a vehicle (set its alias).
     */
    public static function renameVehicle(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        string $name
    ): CommandAck {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner['autoAlias'] = $name;

        $decoded = TokenJson::postTokenJson(
            self::RENAME_ENDPOINT,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        $raw = is_array($decoded) ? $decoded : [];
        $data = array_merge(['vin' => $vin, 'raw' => $raw], $raw);

        return new CommandAck($data);
    }
}
