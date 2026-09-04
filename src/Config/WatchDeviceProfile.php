<?php

declare(strict_types=1);

namespace Byd\ApiClient\Config;

use Byd\ApiClient\Exception\ValidationException;

use function chr;
use function md5;
use function ord;
use function random_bytes;
use function strtoupper;
use function trim;

final readonly class WatchDeviceProfile
{
    public string $watchImei;

    public string $brand;

    public string $model;

    public function __construct(
        string $watchImei,
        string $brand,
        string $model,
        public string $appVersion = '341',
        public string $watchOs = '0',
        public string $networkType = 'wifi',
    ) {
        $watchImei = strtoupper(trim($watchImei));
        if (preg_match('/^[A-F0-9]{32}$/', $watchImei) !== 1) {
            throw new ValidationException('Watch IMEI must be a 32-character hexadecimal pseudo identifier.');
        }

        $brand = strtoupper(trim($brand));
        $model = strtoupper(trim($model));
        if ($brand === '' || $model === '') {
            throw new ValidationException('Watch brand and model must not be empty.');
        }

        $this->watchImei = $watchImei;
        $this->brand = $brand;
        $this->model = $model;
    }

    public static function generate(string $brand, string $model): self
    {
        $uuid = random_bytes(16);
        $uuid[6] = chr((ord($uuid[6]) & 0x0f) | 0x40);
        $uuid[8] = chr((ord($uuid[8]) & 0x3f) | 0x80);
        $uuidHex = strtoupper(bin2hex($uuid));

        return new self(strtoupper(md5($uuidHex)), $brand, $model);
    }

    public function watchName(): string
    {
        return $this->brand.$this->model;
    }
}
