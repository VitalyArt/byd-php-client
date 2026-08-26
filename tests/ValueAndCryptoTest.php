<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\Config\Credentials;
use Byd\ApiClient\Crypto\BangcleCodec;
use Byd\ApiClient\Crypto\Cryptography;
use Byd\ApiClient\Exception\ValidationException;
use Byd\ApiClient\Value\Vin;

use function dirname;

use PHPUnit\Framework\TestCase;

final class ValueAndCryptoTest extends TestCase
{
    public function testVinNormalizesAndValidates(): void
    {
        self::assertSame('LGXCE6CB1N0000001', (new Vin('lgxce6cb1n0000001'))->value);
        $this->expectException(ValidationException::class);
        new Vin('invalid');
    }

    public function testCredentialsRejectInvalidPin(): void
    {
        $this->expectException(ValidationException::class);
        new Credentials('user', 'secret', '12x4');
    }

    public function testAesRoundTripAndStableHashes(): void
    {
        $crypto = new Cryptography();
        self::assertSame('5EB63BBBE01EEED093CB22BB8F5ACDC3', $crypto->md5('hello world'));
        $key = $crypto->loginKey('secret');
        $encrypted = $crypto->encrypt('{"hello":"world"}', $key);
        self::assertSame('{"hello":"world"}', $crypto->decrypt($encrypted, $key));
    }

    public function testAesDecryptAcceptsServerEncryptedEmptyPayload(): void
    {
        $crypto = new Cryptography();
        $keyHex = $crypto->md5('encryption-token');
        $key = hex2bin($keyHex);
        self::assertIsString($key);

        $ciphertext = openssl_encrypt('', 'AES-128-CBC', $key, OPENSSL_RAW_DATA, str_repeat("\0", 16));
        self::assertIsString($ciphertext);

        self::assertSame('', $crypto->decrypt(bin2hex($ciphertext), $keyHex));
    }

    public function testBangcleEnvelopeRoundTrip(): void
    {
        $codec = new BangcleCodec(dirname(__DIR__).'/data/bangcle_tables.bin');
        $payload = '{"request":"typed DTO"}';
        self::assertSame($payload, $codec->decodeEnvelope($codec->encodeEnvelope($payload)));
    }
}
