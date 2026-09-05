<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\BydClient;
use Byd\ApiClient\Contract\NonceGeneratorInterface;
use Byd\ApiClient\Contract\SecureTransportInterface;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Enum\Endpoint;
use Byd\ApiClient\Exception\ApiException;
use Byd\ApiClient\Value\Vin;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

final class ControlServiceTest extends TestCase
{
    public function testVerifyPinAcceptsSuccessfulEmptyEncryptedPayload(): void
    {
        $encryptionToken = 'encryption-token';
        $cryptography = new Cryptography();
        $keyHex = $cryptography->md5($encryptionToken);
        $key = hex2bin($keyHex);
        self::assertIsString($key);
        $ciphertext = openssl_encrypt('', 'AES-128-CBC', $key, OPENSSL_RAW_DATA, str_repeat("\0", 16));
        self::assertIsString($ciphertext);
        $client = $this->clientWithVerifyResponse([
            'code' => '0',
            'message' => 'SUCCESS',
            'respondData' => bin2hex($ciphertext),
        ]);

        $result = $client->controls(new Vin('LGXCE6CB1N0000001'))->verifyPin('123456');

        self::assertTrue($result->isSuccess());
        self::assertSame([], $result->raw);
    }

    public function testVerifyPinReturnsFailedResultForWrongPin(): void
    {
        $client = $this->clientWithVerifyResponse([
            'code' => '5005',
            'message' => 'Wrong operation password, you are allowed to have 4 more attempts today.',
        ]);

        $result = $client->controls(new Vin('LGXCE6CB1N0000001'))->verifyPin('654321');

        self::assertFalse($result->isSuccess());
        self::assertSame(5005, $result->code);
        self::assertSame('Wrong operation password, you are allowed to have 4 more attempts today.', $result->message);
    }

    public function testVerifyPinStillThrowsUnrelatedApiErrors(): void
    {
        $client = $this->clientWithVerifyResponse(['code' => '1008', 'message' => 'Service busy']);

        $this->expectException(ApiException::class);

        $client->controls(new Vin('LGXCE6CB1N0000001'))->verifyPin('123456');
    }

    /** @param array<string, mixed> $verifyResponse */
    private function clientWithVerifyResponse(array $verifyResponse): BydClient
    {
        $password = 'account-password';
        $encryptionToken = 'encryption-token';
        $cryptography = new Cryptography();
        $transport = new readonly class ($cryptography, $password, $encryptionToken, $verifyResponse) implements SecureTransportInterface {
            /** @param array<string, mixed> $verifyResponse */
            public function __construct(
                private Cryptography $cryptography,
                private string $password,
                private string $encryptionToken,
                private array $verifyResponse,
            ) {
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

                TestCase::assertSame(Endpoint::VERIFY_PIN, $endpoint);

                return $this->verifyResponse;
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

        return new BydClient(
            'user@example.com',
            $password,
            clock: $clock,
            nonceGenerator: $nonce,
            transport: $transport,
        );
    }
}
