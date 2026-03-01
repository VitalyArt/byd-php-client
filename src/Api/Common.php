<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;

class Common
{
    /**
     * Build common inner payload fields used by most BYD endpoints.
     *
     * @return array<string, string>
     */
    public static function buildInnerBase(
        BydConfig $config,
        ?int $nowMs = null,
        ?string $vin = null,
        ?string $requestSerial = null
    ): array {
        if ($nowMs === null) {
            $nowMs = (int) (microtime(true) * 1000);
        }

        $inner = [
            'deviceType' => $config->getDevice()->getDeviceType(),
            'imeiMD5' => $config->getDevice()->getImeiMd5(),
            'networkType' => $config->getDevice()->getNetworkType(),
            'random' => strtoupper(bin2hex(random_bytes(16))),
            'timeStamp' => (string) $nowMs,
            'version' => $config->getAppInnerVersion(),
        ];

        if ($vin !== null) {
            $inner['vin'] = $vin;
        }

        if ($requestSerial !== null) {
            $inner['requestSerial'] = $requestSerial;
        }

        return $inner;
    }
}
