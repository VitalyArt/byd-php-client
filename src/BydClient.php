<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Contract\SleeperInterface;
use Byd\ApiClient\Crypto\BangcleCodec;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Infrastructure\SecureNonceGenerator;
use Byd\ApiClient\Infrastructure\SystemClock;
use Byd\ApiClient\Infrastructure\SystemSleeper;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Serialization\ProtocolPayloadNormalizer;
use Byd\ApiClient\Service\ChargingService;
use Byd\ApiClient\Service\ClimateService;
use Byd\ApiClient\Service\ControlService;
use Byd\ApiClient\Service\NotificationService;
use Byd\ApiClient\Service\TelemetryService;
use Byd\ApiClient\Service\VehicleService;
use Byd\ApiClient\Service\VehicleSettingsService;
use Byd\ApiClient\Transport\PsrSecureTransport;
use Byd\ApiClient\Value\Vin;

use function dirname;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final readonly class BydClient
{
    private VehicleService $vehicleService;

    private ProtocolClient $protocol;

    private DtoSerializer $serializer;

    private ProtocolPayloadNormalizer $payloadNormalizer;

    private PollingExecutor $polling;

    private Cryptography $cryptography;

    private SessionManager $sessions;

    public function __construct(
        private ClientConfig $config,
        ?ClientInterface $httpClient = null,
        (RequestFactoryInterface&StreamFactoryInterface)|null $httpFactory = null,
        ?LoggerInterface $logger = null,
        ?ClockInterface $clock = null,
        ?SleeperInterface $sleeper = null,
        ?NonceGeneratorInterface $nonceGenerator = null,
        ?SecureTransportInterface $transport = null,
        ?DtoSerializer $serializer = null,
    ) {
        $this->serializer = $serializer ?? new DtoSerializer();
        $this->payloadNormalizer = new ProtocolPayloadNormalizer($this->serializer);
        $this->cryptography = new Cryptography();
        $clock ??= new SystemClock();
        $sleeper ??= new SystemSleeper();
        $nonceGenerator ??= new SecureNonceGenerator();
        $httpFactory ??= new HttpFactory();
        $transport ??= new PsrSecureTransport($config, $httpClient ?? new Client(['cookies' => true]), $httpFactory, $httpFactory, new BangcleCodec(dirname(__DIR__).'/data/bangcle_tables.bin'), $this->serializer, $logger ?? new NullLogger());
        $authentication = new AuthenticationService($config, $transport, $this->serializer, $this->cryptography, $clock, $nonceGenerator);
        $this->sessions = new SessionManager($authentication, $clock);
        $this->protocol = new ProtocolClient($config, $this->sessions, $transport, $this->serializer, $this->payloadNormalizer, $this->cryptography, $clock, $nonceGenerator);
        $this->polling = new PollingExecutor($config->polling, $clock, $sleeper);
        $this->vehicleService = new VehicleService($this->protocol, $this->serializer);
    }

    public function authenticate(): void
    {
        $this->sessions->refresh();
    }

    public function invalidateSession(): void
    {
        $this->sessions->invalidate();
    }

    public function vehicles(): VehicleService
    {
        return $this->vehicleService;
    }

    public function telemetry(Vin $vin): TelemetryService
    {
        return new TelemetryService($vin, $this->config, $this->vehicleService, $this->protocol, $this->serializer, $this->polling);
    }

    public function climate(Vin $vin): ClimateService
    {
        return new ClimateService($vin, $this->protocol, $this->serializer, $this->controls($vin));
    }

    public function charging(Vin $vin): ChargingService
    {
        return new ChargingService($vin, $this->protocol, $this->serializer, $this->polling);
    }

    public function controls(Vin $vin): ControlService
    {
        return new ControlService($vin, $this->config, $this->protocol, $this->serializer, $this->payloadNormalizer, $this->polling, $this->cryptography);
    }

    public function notifications(Vin $vin): NotificationService
    {
        return new NotificationService($vin, $this->protocol, $this->serializer);
    }

    public function settings(Vin $vin): VehicleSettingsService
    {
        return new VehicleSettingsService($vin, $this->protocol, $this->serializer);
    }
}
