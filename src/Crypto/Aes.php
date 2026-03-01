<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use Byd\ApiClient\Exceptions\BangcleException;

use function chr;

use Exception;

use function in_array;

use JsonException;

use function ord;
use function strlen;

/**
 * Standard AES-128-CBC encryption for BYD inner payloads.
 */
class Aes
{
    private const ZERO_IV = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";

    /**
     * Parse hex string to bytes.
     *
     * @throws BangcleException
     */
    private static function parseHexBytes(string $value, string $name, ?array $allowedNbytes = null): string
    {
        $text = trim($value);
        if (stripos($text, '0x') === 0) {
            $text = substr($text, 2);
        }

        if ($text === '') {
            throw new BangcleException("{$name} is empty");
        }

        if (strlen($text) % 2 !== 0) {
            throw new BangcleException("{$name} hex length must be even (got " . strlen($text) . ')');
        }

        if (!ctype_xdigit($text)) {
            throw new BangcleException("{$name} must be hex-encoded");
        }

        $data = hex2bin($text);

        if ($allowedNbytes !== null && !in_array(strlen($data), $allowedNbytes, true)) {
            $allowed = implode(', ', $allowedNbytes);

            throw new BangcleException("{$name} must be {$allowed} bytes (got " . strlen($data) . ')');
        }

        return $data;
    }

    /**
     * AES-128-CBC encrypt with zero IV, returning uppercase hex.
     *
     * @throws BangcleException
     */
    public static function aesEncryptHex(string $plaintext, string $keyHex): string
    {
        try {
            $key = self::parseHexBytes($keyHex, 'AES key', [16, 24, 32]);
            $padded = self::padPKCS7($plaintext, 16);
            $cipher = openssl_encrypt($padded, 'AES-' . (strlen($key) * 8) . '-CBC', $key, OPENSSL_RAW_DATA, self::ZERO_IV);

            if ($cipher === false) {
                throw new Exception('Encryption failed');
            }

            return strtoupper(bin2hex($cipher));
        } catch (Exception $e) {
            throw new BangcleException('AES encryption failed: ' . $e->getMessage());
        }
    }

    /**
     * AES-128-CBC decrypt from hex with zero IV, returning UTF-8 string.
     *
     * @throws BangcleException
     */
    public static function aesDecryptUtf8(string $cipherHex, string $keyHex): string
    {
        try {
            $key = self::parseHexBytes($keyHex, 'AES key', [16, 24, 32]);
            $ct = self::parseHexBytes($cipherHex, 'AES ciphertext');
            $decrypted = openssl_decrypt($ct, 'AES-' . (strlen($key) * 8) . '-CBC', $key, OPENSSL_RAW_DATA, self::ZERO_IV);

            if ($decrypted === false) {
                throw new Exception('Decryption failed');
            }

            try {
                json_decode($decrypted, true, JSON_THROW_ON_ERROR);

                return $decrypted;
            } catch (JsonException) {
                return self::unpadPKCS7($decrypted);
            }

        } catch (BangcleException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new BangcleException('AES decryption failed: ' . $e->getMessage());
        }
    }

    /**
     * PKCS#7 padding.
     */
    private static function padPKCS7(string $data, int $blockSize): string
    {
        $pad = $blockSize - (strlen($data) % $blockSize);

        return $data . str_repeat(chr($pad), $pad);
    }

    /**
     * PKCS#7 unpadding.
     */
    private static function unpadPKCS7(string $data): string
    {
        $pad = ord($data[strlen($data) - 1]);

        return substr($data, 0, -$pad);
    }
}
