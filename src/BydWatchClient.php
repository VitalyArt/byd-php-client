<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SleeperInterface;
use Byd\ApiClient\Contract\WatchTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Crypto\WatchCryptography;
use Byd\ApiClient\Dto\Response\WatchBluetoothInfo;
use Byd\ApiClient\Dto\Response\WatchQrSession;
use Byd\ApiClient\Dto\Response\WatchQrStatusResponse;
use Byd\ApiClient\Dto\Response\WatchServerTime;
use Byd\ApiClient\Dto\Response\WatchTokenInfo;
use Byd\ApiClient\Dto\Response\WatchTokenResponse;
use Byd\ApiClient\Dto\Response\WatchVehicleConfiguration;
use Byd\ApiClient\Enum\WatchQrStatus;
use Byd\ApiClient\Exception\WatchAuthorizationException;
use Byd\ApiClient\Infrastructure\SecureNonceGenerator;
use Byd\ApiClient\Infrastructure\SystemClock;
use Byd\ApiClient\Infrastructure\SystemSleeper;
use Byd\ApiClient\Serialization\DtoSerializer;
use Byd\ApiClient\Transport\PsrWatchTransport;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;
use Psr\Clock\ClockInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final readonly class BydWatchClient
{
    private WatchProtocolClient $protocol;

    private PollingExecutor $polling;

    public function __construct(
        WatchClientConfig $config,
        ?ClientInterface $httpClient = null,
        (RequestFactoryInterface&StreamFactoryInterface)|null $httpFactory = null,
        ?LoggerInterface $logger = null,
        ?ClockInterface $clock = null,
        ?SleeperInterface $sleeper = null,
        ?NonceGeneratorInterface $nonceGenerator = null,
        ?WatchTransportInterface $transport = null,
        ?DtoSerializer $serializer = null,
    ) {
        $clock ??= new SystemClock();
        $sleeper ??= new SystemSleeper();
        $nonceGenerator ??= new SecureNonceGenerator();
        $serializer ??= new DtoSerializer();
        $httpFactory ??= new HttpFactory();
        $transport ??= new PsrWatchTransport(
            $config,
            $httpClient ?? new Client(),
            $httpFactory,
            $httpFactory,
            $serializer,
            $logger ?? new NullLogger(),
        );
        $this->protocol = new WatchProtocolClient(
            $config,
            $transport,
            $serializer,
            new WatchCryptography(new Cryptography()),
            $clock,
            $nonceGenerator,
        );
        $this->polling = new PollingExecutor($config->polling, $clock, $sleeper);
    }

    /** Synchronize the watch protocol clock with the BYD server. */
    public function synchronizeServerTime(): WatchServerTime
    {
        return $this->protocol->synchronizeServerTime();
    }

    /** Create a QR authorization session for the watch. */
    public function createQrSession(): WatchQrSession
    {
        return $this->protocol->createQrSession();
    }

    /** Check the current status of a QR authorization session. */
    public function checkQrSession(WatchQrSession $session): WatchQrStatusResponse
    {
        return $this->protocol->checkQrSession($session);
    }

    /** Poll a QR session until it reaches a terminal status. */
    public function waitForAuthorization(WatchQrSession $session): WatchQrStatusResponse
    {
        return $this->polling->until(
            fn (): WatchQrStatusResponse => $this->checkQrSession($session),
            static fn (WatchQrStatusResponse $status): bool => $status->status->isTerminal(),
        );
    }

    /** Poll a QR session and exchange an approved session for a watch token. */
    public function authorize(WatchQrSession $session): WatchTokenResponse
    {
        $status = $this->waitForAuthorization($session);
        if ($status->status !== WatchQrStatus::APPROVED) {
            throw new WatchAuthorizationException($status->status);
        }

        return $this->protocol->gainToken($session);
    }

    /** Exchange an approved QR session for a watch token without polling. */
    public function gainToken(WatchQrSession $session): WatchTokenResponse
    {
        return $this->protocol->gainToken($session);
    }

    /** Retrieve the vehicle configuration associated with a watch token. */
    public function vehicle(WatchTokenInfo $token): WatchVehicleConfiguration
    {
        return $this->protocol->gainVehicle($token);
    }

    /** Retrieve Bluetooth digital-key information for a watch token. */
    public function bluetooth(WatchTokenInfo $token): WatchBluetoothInfo
    {
        return $this->protocol->gainBluetooth($token);
    }

    /** Invalidate the watch token at the BYD backend. */
    public function logout(WatchTokenInfo $token): void
    {
        $this->protocol->logout($token);
    }
}
