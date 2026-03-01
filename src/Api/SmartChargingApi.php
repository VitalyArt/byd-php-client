<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\CommandAck;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class SmartChargingApi
{
    private const TOGGLE_ENDPOINT = '/control/smartCharge/changeChargeStatue';
    private const SAVE_ENDPOINT = '/control/smartCharge/saveOrUpdate';

    /**
     * Toggle smart charging on or off.
     */
    public static function toggleSmartCharging(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        bool $enable
    ): CommandAck {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner['smartChargeSwitch'] = $enable ? '1' : '0';

        $decoded = TokenJson::postTokenJson(
            self::TOGGLE_ENDPOINT,
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

    /**
     * Save a smart charging schedule.
     *
     * @param int $targetSoc Target state of charge (0-100)
     * @param int $startHour Scheduled start hour (0-23)
     * @param int $startMinute Scheduled start minute (0-59)
     * @param int $endHour Scheduled end hour (0-23)
     * @param int $endMinute Scheduled end minute (0-59)
     */
    public static function saveChargingSchedule(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        int $targetSoc,
        int $startHour,
        int $startMinute,
        int $endHour,
        int $endMinute
    ): CommandAck {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner = array_merge($inner, [
            'endHour' => (string) $endHour,
            'endMinute' => (string) $endMinute,
            'startHour' => (string) $startHour,
            'startMinute' => (string) $startMinute,
            'targetSoc' => (string) $targetSoc,
        ]);

        $decoded = TokenJson::postTokenJson(
            self::SAVE_ENDPOINT,
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
