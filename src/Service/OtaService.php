<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Dto\Request\OtaCommandRequest;
use Byd\ApiClient\Dto\Request\OtaVersionRequest;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Dto\Response\OtaUpdateInfo;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Value\Vin;

use function ctype_xdigit;

use SensitiveParameter;

use function strlen;
use function strtoupper;
use function trim;

final readonly class OtaService
{
    public function __construct(private Vin $vin, private ClientConfig $config, private ProtocolClient $protocol, private DtoSerializer $serializer, private Cryptography $cryptography)
    {
    }

    /** Fetch installed and available OTA version information. */
    public function status(): OtaUpdateInfo
    {
        $request = new OtaVersionRequest($this->vin, $this->config->locale->language);

        return $this->serializer->denormalize($this->protocol->request(Endpoint::OTA_VERSION, $request), OtaUpdateInfo::class);
    }

    /** Book an available OTA update. */
    public function book(#[SensitiveParameter] ?string $pin = null): CommandResult
    {
        return $this->command(Endpoint::OTA_BOOKING, $pin);
    }

    /** Cancel a previously booked OTA update. */
    public function cancelBooking(#[SensitiveParameter] ?string $pin = null): CommandResult
    {
        return $this->command(Endpoint::OTA_CANCEL_BOOKING, $pin);
    }

    /** Start an OTA update. */
    public function start(#[SensitiveParameter] ?string $pin = null): CommandResult
    {
        return $this->command(Endpoint::OTA_UPGRADE, $pin);
    }

    private function command(Endpoint $endpoint, ?string $pin): CommandResult
    {
        $request = new OtaCommandRequest($this->vin, $this->config->locale->language, $this->password($pin));

        return $this->serializer->denormalize($this->protocol->request($endpoint, $request), CommandResult::class);
    }

    private function password(?string $pin): string
    {
        $value = trim($pin ?? $this->config->credentials->controlPin ?? '');
        if ($value === '') {
            return '';
        }

        return strlen($value) === 32 && ctype_xdigit($value) ? strtoupper($value) : $this->cryptography->md5($value);
    }
}
