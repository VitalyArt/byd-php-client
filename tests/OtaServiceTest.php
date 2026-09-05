<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\BydClient;
use Byd\ApiClient\Config\Locale;
use Byd\ApiClient\Config\ProtocolOptions;
use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Config\WatchDeviceProfile;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Enum\ApiRegion;
use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Enum\OtaUpgradeStatus;
use Byd\ApiClient\Value\Vin;

use function count;

use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

final class OtaServiceTest extends TestCase
{
    public function testReadsOtaUpdateInformation(): void
    {
        $cryptography = new Cryptography();
        $password = 'account-password';
        $encryptionToken = 'encryption-token';
        $transport = new readonly class ($cryptography, $password, $encryptionToken) implements SecureTransportInterface {
            public function __construct(private Cryptography $cryptography, private string $password, private string $encryptionToken)
            {
            }

            public function send(Endpoint $endpoint, object $request): array
            {
                if ($endpoint === Endpoint::LOGIN) {
                    return [
                        'code' => '0',
                        'respondData' => $this->cryptography->encrypt(json_encode([
                            'token' => [
                                'userId' => 'user-id',
                                'signToken' => 'sign-token',
                                'encryToken' => $this->encryptionToken,
                            ],
                        ], JSON_THROW_ON_ERROR), $this->cryptography->loginKey($this->password)),
                    ];
                }

                TestCase::assertSame(Endpoint::OTA_VERSION, $endpoint);
                $payload = [
                    'bookingTimeStamp' => 0,
                    'currentVersion' => '1.0.0',
                    'updateVersion' => '1.1.0',
                    'estimateUpgradeTime' => 1800,
                    'padTimeZone' => 'Europe/Amsterdam',
                    'functionDescription' => 'System update',
                    'upgradeStatus' => 1,
                    'functionAddition' => 'New feature',
                    'functionOptimization' => 'Faster startup',
                    'acknowledgements' => '',
                    'currentUpdateTime' => 0,
                    'upgradeResult' => ['status' => 'ready', 'message' => 'OK', 'statusCode' => '0'],
                ];

                return [
                    'code' => '0',
                    'respondData' => $this->cryptography->encrypt(json_encode($payload, JSON_THROW_ON_ERROR), $this->cryptography->md5($this->encryptionToken)),
                ];
            }
        };
        $clock = new class () implements ClockInterface {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable('2026-01-01T00:00:00Z');
            }
        };
        $nonce = new class () implements NonceGeneratorInterface {
            public function generate(): string
            {
                return '0123456789ABCDEF0123456789ABCDEF';
            }
        };
        $client = new BydClient(
            'user@example.com',
            $password,
            clock: $clock,
            nonceGenerator: $nonce,
            transport: $transport,
        );

        $update = $client->ota(new Vin('LGXCE6CB1N0000001'))->status();

        self::assertTrue($update->hasUpdate());
        self::assertSame('1.1.0', $update->updateVersion);
        self::assertSame(OtaUpgradeStatus::UPGRADE_AVAILABLE, $update->status);
        self::assertSame('ready', $update->upgradeResult?->status);
    }

    public function testSendsConfirmedOtaCommands(): void
    {
        $cryptography = new Cryptography();
        $password = 'account-password';
        $encryptionToken = 'encryption-token';
        $transport = new readonly class ($cryptography, $password, $encryptionToken) implements SecureTransportInterface {
            public function __construct(private Cryptography $cryptography, private string $password, private string $encryptionToken)
            {
            }

            public function send(Endpoint $endpoint, object $request): array
            {
                if ($endpoint === Endpoint::LOGIN) {
                    return [
                        'code' => '0',
                        'respondData' => $this->cryptography->encrypt(json_encode([
                            'token' => ['userId' => 'user-id', 'signToken' => 'sign-token', 'encryToken' => $this->encryptionToken],
                        ], JSON_THROW_ON_ERROR), $this->cryptography->loginKey($this->password)),
                    ];
                }

                TestCase::assertContains($endpoint, [Endpoint::OTA_BOOKING, Endpoint::OTA_CANCEL_BOOKING, Endpoint::OTA_UPGRADE]);

                return ['code' => '0', 'respondData' => $this->cryptography->encrypt('{}', $this->cryptography->md5($this->encryptionToken))];
            }
        };
        $client = new BydClient('user@example.com', $password, transport: $transport);
        $ota = $client->ota(new Vin('LGXCE6CB1N0000001'));

        self::assertTrue($ota->book('1234')->isSuccess());
        self::assertTrue($ota->cancelBooking('1234')->isSuccess());
        self::assertTrue($ota->start('1234')->isSuccess());
    }

    #[DataProvider('regionalUrls')]
    public function testRegionalNodeResolution(CountryCode $countryCode, int $node, string $url): void
    {
        $region = ApiRegion::forCountry($countryCode);
        self::assertSame($node, $region->node());
        self::assertSame($url, $region->value);
        self::assertSame($url, ProtocolOptions::forCountry($countryCode)->baseUrl);
        self::assertSame($url, ProtocolOptions::forRegion($region)->baseUrl);

        $watchConfig = new WatchClientConfig(
            new WatchDeviceProfile('0123456789ABCDEF0123456789ABCDEF', 'SAMSUNG', 'SM-R890'),
            new Locale($countryCode, 'en', 'UTC'),
        );
        self::assertSame($url, $watchConfig->resolvedBaseUrl());
    }

    public function testAllBundledCountriesMapBackToTheirRegion(): void
    {
        $countries = [];
        foreach (ApiRegion::cases() as $region) {
            foreach ($region->countries() as $countryCode) {
                self::assertArrayNotHasKey($countryCode->value, $countries);
                self::assertSame($region, ApiRegion::forCountry($countryCode));
                $countries[$countryCode->value] = true;
            }
        }

        self::assertCount(116, CountryCode::cases());
        self::assertCount(count(CountryCode::cases()), $countries);
        self::assertSame(CountryCode::TH, new Locale(CountryCode::TH, 'en', 'Asia/Bangkok')->countryCode);
    }

    /** @return iterable<string, array{CountryCode, int, string}> */
    public static function regionalUrls(): iterable
    {
        yield 'Norway uses the European node' => [CountryCode::NO, 1, 'https://dilinkappoversea-eu.byd.auto'];
        yield 'Thailand uses the Singapore node' => [CountryCode::TH, 2, 'https://dilinkappoversea-sg.byd.auto'];
        yield 'United Arab Emirates uses node 7' => [CountryCode::AE, 7, 'https://dilinkappoversea-no.byd.auto'];
        yield 'Vietnam uses node 13' => [CountryCode::VN, 13, 'https://dilinkappoversea-vn.byd.auto'];
        yield 'Kazakhstan uses node 16' => [CountryCode::KZ, 16, 'https://dilinkappoversea-kz.byd.auto'];
    }
}
