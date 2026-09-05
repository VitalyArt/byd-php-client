<?php

declare(strict_types=1);

namespace Byd\ApiClient\Service;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Dto\Request\BatteryHeatRequest;
use Byd\ApiClient\Dto\Request\RemoteControlRequest;
use Byd\ApiClient\Dto\Request\SeatClimateRequest;
use Byd\ApiClient\Dto\Request\VerifyControlPinRequest;
use Byd\ApiClient\Dto\Response\CommandResult;
use Byd\ApiClient\Enum\ApiErrorCode;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Enum\RemoteCommand;
use Byd\ApiClient\Exception\ApiException;
use Byd\ApiClient\Exception\RemoteControlException;
use Byd\ApiClient\PollingExecutor;
use Byd\ApiClient\ProtocolClient;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Serialization\ProtocolPayloadNormalizer;
use Byd\ApiClient\Value\Vin;

use function in_array;
use function is_string;
use function strlen;

final readonly class ControlService
{
    public function __construct(private Vin $vin, private ClientConfig $config, private ProtocolClient $protocol, private DtoSerializer $serializer, private ProtocolPayloadNormalizer $payloadNormalizer, private PollingExecutor $polling, private Cryptography $cryptography)
    {
    }

    /** Verify the configured or supplied control PIN. */
    public function verifyPin(?string $pin = null): CommandResult
    {
        try {
            $raw = $this->protocol->request(Endpoint::VERIFY_PIN, new VerifyControlPinRequest($this->vin, $this->password($pin)));
        } catch (ApiException $exception) {
            if (!in_array($exception->knownCode, [ApiErrorCode::INVALID_CONTROL_PIN, ApiErrorCode::CONTROL_PIN_LOCKED, ApiErrorCode::CONTROL_PIN_REQUIRED], true)) {
                throw $exception;
            }

            $raw = [
                'code' => $exception->apiCode,
                'message' => $exception->getMessage(),
            ];
        }

        return $this->serializer->denormalize($raw, CommandResult::class);
    }

    /** @param array<string, mixed>|null $parameters */
    /** Execute a remote command and wait for its terminal result. */
    public function execute(RemoteCommand $command, ?array $parameters = null, ?string $pin = null): CommandResult
    {
        $serial = null;
        $result = $this->polling->until(
            function () use ($command, $parameters, $pin, &$serial): CommandResult {
                $endpoint = $serial === null ? Endpoint::REMOTE_CONTROL : Endpoint::REMOTE_CONTROL_RESULT;
                $raw = $this->protocol->request($endpoint, new RemoteControlRequest($this->vin, $command, $serial === null ? $this->password($pin) : '', $serial === null ? $parameters : null, $serial));
                $serial = is_string($raw['requestSerial'] ?? null) ? $raw['requestSerial'] : $serial;

                return $this->serializer->denormalize($raw, CommandResult::class);
            },
            static fn (CommandResult $result): bool => $result->isTerminal(),
        );
        if (!$result->isSuccess()) {
            throw new RemoteControlException("Remote command {$command->name} failed: {$result->message}");
        }

        return $result;
    }

    public function lock(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::LOCK, pin: $pin);
    }

    public function unlock(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::UNLOCK, pin: $pin);
    }

    public function flashLights(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::FLASH_LIGHTS, pin: $pin);
    }

    public function findCar(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::FIND_CAR, pin: $pin);
    }

    public function openWindows(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::OPEN_WINDOWS, pin: $pin);
    }

    public function closeWindows(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::CLOSE_WINDOWS, pin: $pin);
    }

    public function openTrunk(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::OPEN_TRUNK, pin: $pin);
    }

    public function closeTrunk(?string $pin = null): CommandResult
    {
        return $this->execute(RemoteCommand::CLOSE_TRUNK, pin: $pin);
    }

    public function setSeatClimate(SeatClimateRequest $request, ?string $pin = null): CommandResult
    {
        return $this->executeDto(RemoteCommand::SEAT_CLIMATE, $request, $pin);
    }

    public function setBatteryHeat(BatteryHeatRequest $request, ?string $pin = null): CommandResult
    {
        return $this->executeDto(RemoteCommand::BATTERY_HEAT, $request, $pin);
    }

    public function executeDto(RemoteCommand $command, object $request, ?string $pin = null): CommandResult
    {
        return $this->execute($command, $this->payloadNormalizer->normalize($request), $pin);
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
