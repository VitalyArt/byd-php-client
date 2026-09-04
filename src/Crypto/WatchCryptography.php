<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use Byd\ApiClient\Exception\ProtocolException;

use function chr;
use function ctype_xdigit;
use function hex2bin;
use function is_array;
use function is_scalar;
use function is_string;
use function json_decode;
use function json_encode;
use function ksort;
use function openssl_decrypt;
use function openssl_encrypt;
use function ord;
use function sprintf;
use function str_repeat;
use function strlen;
use function strtoupper;
use function substr;

use Throwable;

use function trim;

final readonly class WatchCryptography
{
    private const string QR_KEY_SEED = 'watch.bydautolink';

    private const string ZERO_IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    public function __construct(private Cryptography $cryptography = new Cryptography())
    {
    }

    public function md5(string $value): string
    {
        return $this->cryptography->md5($value);
    }

    public function qrPayload(string $watchImei, string $uuid, string $countryCode): string
    {
        $plaintext = "watchImei={$watchImei}&uuid={$uuid}&countryCode={$countryCode}";

        return 'watchQRCode://'.$this->encrypt($plaintext, $this->md5(self::QR_KEY_SEED));
    }

    /** @param array<string, mixed> $fields */
    public function encryptFields(array $fields, string $keyHex): string
    {
        ksort($fields);
        $json = json_encode($fields, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

        return $this->encrypt($json, $keyHex);
    }

    /** @param array<string, scalar|null> $fields */
    public function sign(array $fields, string $password): string
    {
        ksort($fields);
        $parts = [];
        foreach ($fields as $name => $value) {
            if ($value !== null && !is_scalar($value)) {
                throw new ProtocolException("Watch signing field {$name} is not scalar.");
            }

            $parts[] = $name.'='.($value ?? '');
        }

        $digest = sha1(implode('&', $parts).'&password='.$this->md5($password), true);
        $signature = '';
        foreach (str_split($digest) as $index => $byte) {
            $hex = sprintf('%x', ord($byte));
            $signature .= $index % 2 === 0 ? strtoupper($hex) : $hex;
        }

        return $signature;
    }

    /** @return array<string, mixed> */
    public function decryptResponse(mixed $respondData, string $keyHex): array
    {
        if ($respondData === null || $respondData === '') {
            return [];
        }

        if (!is_string($respondData)) {
            throw new ProtocolException('Watch respondData must be an encrypted string.');
        }

        $current = trim($respondData);

        try {
            for ($layer = 0; $layer < 2; ++$layer) {
                $decoded = json_decode($current, true);
                if (is_array($decoded)) {
                    return $this->stringKeyed($decoded);
                }

                if (is_string($decoded)) {
                    $current = $decoded;
                }

                if ($current === '') {
                    return [];
                }

                if (strlen($current) % 2 !== 0 || !ctype_xdigit($current)) {
                    throw new ProtocolException('Watch response is neither JSON nor hexadecimal ciphertext.');
                }

                $current = $this->decrypt($current, $keyHex);
            }

            $decoded = json_decode($current, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            if ($exception instanceof ProtocolException) {
                throw $exception;
            }

            throw new ProtocolException('Unable to decrypt watch response.', $exception->getCode(), previous: $exception);
        }

        if (!is_array($decoded)) {
            throw new ProtocolException('Decrypted watch response is not a JSON object.');
        }

        return $this->stringKeyed($decoded);
    }

    /**
     * @param array<array-key, mixed> $values
     * @return array<string, mixed>
     */
    private function stringKeyed(array $values): array
    {
        $result = [];
        foreach ($values as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    private function encrypt(string $plaintext, string $keyHex): string
    {
        $key = $this->hex($keyHex, 'key');
        $padding = 16 - strlen($plaintext) % 16;
        $ciphertext = openssl_encrypt(
            $plaintext.str_repeat(chr($padding), $padding),
            'AES-'.(strlen($key) * 8).'-CBC',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            self::ZERO_IV,
        );
        if ($ciphertext === false) {
            throw new ProtocolException('Watch AES encryption failed.');
        }

        return strtoupper(bin2hex($ciphertext));
    }

    private function decrypt(string $cipherHex, string $keyHex): string
    {
        $key = $this->hex($keyHex, 'key');
        $ciphertext = $this->hex($cipherHex, 'ciphertext');
        $plaintext = openssl_decrypt(
            $ciphertext,
            'AES-'.(strlen($key) * 8).'-CBC',
            $key,
            OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING,
            self::ZERO_IV,
        );
        if ($plaintext === false || $plaintext === '') {
            if ($plaintext === '') {
                return '';
            }

            throw new ProtocolException('Watch AES decryption failed.');
        }

        $padding = ord($plaintext[strlen($plaintext) - 1]);
        if ($padding < 1 || $padding > 16 || substr($plaintext, -$padding) !== str_repeat(chr($padding), $padding)) {
            throw new ProtocolException('Invalid watch AES padding.');
        }

        return substr($plaintext, 0, -$padding);
    }

    private function hex(string $hex, string $name): string
    {
        $hex = trim($hex);
        if ($hex === '' || strlen($hex) % 2 !== 0 || !ctype_xdigit($hex)) {
            throw new ProtocolException("Invalid watch AES {$name}.");
        }

        $binary = hex2bin($hex);

        return $binary === false ? throw new ProtocolException("Invalid watch AES {$name}.") : $binary;
    }
}
