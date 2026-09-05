<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\BydWatchClient;
use Byd\ApiClient\Config\Locale;
use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Config\WatchDeviceProfile;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\WatchTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Crypto\WatchCryptography;
use Byd\ApiClient\Dto\Protocol\WatchAccountRequest;
use Byd\ApiClient\Dto\Protocol\WatchCommonRequest;
use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Enum\WatchEndpoint;
use Byd\ApiClient\Enum\WatchQrStatus;
use Byd\ApiClient\Infrastructure\SystemSleeper;
use Byd\ApiClient\Serialization\DtoSerializer;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

final class WatchClientTest extends TestCase
{
    public function testQrPayloadMatchesDecompiledWearApplication(): void
    {
        $crypto = new WatchCryptography();
        self::assertSame(
            'watchQRCode://E43244DDB9BBC2D04855A535CDE30BB5B91DD704766CD25A00AB128FC68CDD42DF8BE348A0FCA2DFA3B099BB296FDC6E0CA95D386BDED8B8D099267DA4948C2E95167243A65A36E4CF2E3C56F96DB807D71D5BD21C027BB0A18A9B59727F65E7E5C219D8A26515F6C526BBD1DFB643A8',
            $crypto->qrPayload(
                '0123456789ABCDEF0123456789ABCDEF',
                '123e4567-e89b-12d3-a456-426614174000',
                'UZ',
            ),
        );
    }

    public function testCreateRequestAndAuthorizationFlow(): void
    {
        $crypto = new WatchCryptography(new Cryptography());
        $transport = new class ($crypto) implements WatchTransportInterface {
            public ?WatchAccountRequest $createRequest = null;

            public ?WatchCommonRequest $vehicleRequest = null;

            public function __construct(private readonly WatchCryptography $crypto)
            {
            }

            public function send(WatchEndpoint $endpoint, object $request): array
            {
                $countryKey = $this->crypto->md5('UZ');
                $data = match ($endpoint) {
                    WatchEndpoint::CREATE_QR_CODE => $this->create($request),
                    WatchEndpoint::CHECK_QR_CODE => ['status' => 2],
                    WatchEndpoint::GAIN_TOKEN => [
                        'watchTokenInfo' => [
                            'accountStatus' => 0,
                            'userId' => 'user-id',
                            'watchImei' => '0123456789ABCDEF0123456789ABCDEF',
                            'encryToken' => 'encryption-token',
                            'signToken' => 'sign-token',
                            'timeStamp' => 1700000000000,
                            'language' => 'ru',
                            'vin' => 'LGXCE6CB1N0000001',
                            'userType' => '1',
                        ],
                        'controlPwd' => 'secret-control-password',
                    ],
                    WatchEndpoint::GAIN_VEHICLE => $this->vehicle($request),
                    default => [],
                };
                $key = $endpoint === WatchEndpoint::GAIN_VEHICLE ? $this->crypto->md5('encryption-token') : $countryKey;

                return [
                    'code' => '0',
                    'respondData' => $this->crypto->encryptFields($data, $key),
                ];
            }

            /** @return array<string, mixed> */
            private function create(object $request): array
            {
                TestCase::assertInstanceOf(WatchAccountRequest::class, $request);
                $this->createRequest = $request;

                return [
                    'watchImei' => '0123456789ABCDEF0123456789ABCDEF',
                    'uuid' => '123e4567-e89b-12d3-a456-426614174000',
                    'status' => 0,
                ];
            }

            /** @return array<string, mixed> */
            private function vehicle(object $request): array
            {
                TestCase::assertInstanceOf(WatchCommonRequest::class, $request);
                $this->vehicleRequest = $request;

                return ['modelNameOut' => 'BYD SEALION 7', 'cfVechicle' => ['feature' => true]];
            }
        };
        $client = $this->client($transport);

        $session = $client->createQrSession();
        self::assertSame(WatchQrStatus::WAITING_FOR_SCAN, $session->status);
        $createRequest = $transport->createRequest;
        self::assertNotNull($createRequest);
        self::assertSame(
            '9164FB68B2c5B0924Adf1D967D921E606A8dD4cd',
            $createRequest->sign,
        );
        self::assertSame(
            'AC049E0402A129FC30284DCDB4F4D14E8CDD8186D9925E36A0A074B1491F118CE9E06B2DBBB3A64FFC1D5DCF9A49759C1620A84FBC13529D2B708C69943F22E964EE0526A1D8E3F9C444B67A8507F090E0BA0710F8593031DB5D590195B3B7605CAF75DA94A8E130D52465697838C2E3',
            $createRequest->encryptedData,
        );

        $token = $client->authorize($session);
        self::assertSame('user-id', $token->token->userId);
        self::assertSame('secret-control-password', $token->controlPassword);
        $vehicle = $client->vehicle($token->token);
        self::assertSame('BYD SEALION 7', $vehicle->modelName);
        self::assertInstanceOf(WatchCommonRequest::class, $transport->vehicleRequest);
    }

    private function client(WatchTransportInterface $transport): BydWatchClient
    {
        $clock = new class () implements ClockInterface {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable('2023-11-14T22:13:20.000Z');
            }
        };
        $nonce = new class () implements NonceGeneratorInterface {
            public function generate(): string
            {
                return '00112233445566778899AABBCCDDEEFF';
            }
        };

        return new BydWatchClient(
            new WatchClientConfig(
                new WatchDeviceProfile('0123456789ABCDEF0123456789ABCDEF', 'SAMSUNG', 'SM-R890'),
                new Locale(CountryCode::UZ, 'ru', 'Asia/Tashkent'),
            ),
            clock: $clock,
            sleeper: new SystemSleeper(),
            nonceGenerator: $nonce,
            transport: $transport,
            serializer: new DtoSerializer(),
        );
    }
}
