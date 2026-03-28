<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\CommandAck;
use Byd\ApiClient\Models\PushNotificationState;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class PushNotificationsApi
{
    private const GET_ENDPOINT = '/app/push/getPushSwitchState';

    private const SET_ENDPOINT = '/app/push/setPushSwitchState';

    /**
     * Fetch the current push notification state for a vehicle.
     */
    public static function fetchPushState(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin
    ): PushNotificationState {
        $inner = Common::buildInnerBase($config, null, $vin);

        $decoded = TokenJson::postTokenJson(
            self::GET_ENDPOINT,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        $raw = is_array($decoded) ? $decoded : [];
        $data = array_merge(['vin' => $vin], $raw);

        return new PushNotificationState($data);
    }

    /**
     * Set the push notification state for a vehicle.
     */
    public static function setPushState(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        bool $enable
    ): CommandAck {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner['pushSwitch'] = $enable ? '1' : '0';

        $decoded = TokenJson::postTokenJson(
            self::SET_ENDPOINT,
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
