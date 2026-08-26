<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use Byd\ApiClient\Exception\ProtocolException;

use function chr;
use function is_scalar;
use function ord;

use SensitiveParameter;

use function sprintf;
use function strlen;

final class Cryptography
{
    private const string ZERO_IV = "\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0";

    public function md5(string $input): string
    {
        return strtoupper(md5($input));
    }

    public function loginKey(#[SensitiveParameter] string $password): string
    {
        return $this->md5($this->md5($password));
    }

    /** @param array<string, mixed> $fields */
    public function sign(array $fields, #[SensitiveParameter] string $key): string
    {
        ksort($fields);
        $parts = [];
        foreach ($fields as $name => $value) {
            if ($value !== null && !is_scalar($value)) {
                throw new ProtocolException("Signing field {$name} is not scalar.");
            }

            $parts[] = $name.'='.($value ?? 'null');
        }

        $digest = sha1(implode('&', $parts).'&password='.$key, true);
        $mixed = '';
        foreach (str_split($digest) as $index => $byte) {
            $hex = sprintf('%02x', ord($byte));
            $mixed .= $index % 2 === 0 ? strtoupper($hex) : strtolower($hex);
        }

        $result = '';
        foreach (str_split($mixed) as $index => $character) {
            if ($character !== '0' || $index % 2 !== 0) {
                $result .= $character;
            }
        }

        return $result;
    }

    /** @param array<string, mixed> $payload */
    public function checkcode(array $payload): string
    {
        $json = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $hash = md5($json);

        return substr($hash, 24, 8).substr($hash, 8, 8).substr($hash, 16, 8).substr($hash, 0, 8);
    }

    public function encrypt(string $plaintext, #[SensitiveParameter] string $keyHex): string
    {
        $key = $this->hex($keyHex, 'key');
        $padding = 16 - strlen($plaintext) % 16;
        $ciphertext = openssl_encrypt($plaintext.str_repeat(chr($padding), $padding), 'AES-'.(strlen($key) * 8).'-CBC', $key, OPENSSL_RAW_DATA, self::ZERO_IV);
        if ($ciphertext === false) {
            throw new ProtocolException('AES encryption failed.');
        }

        return strtoupper(bin2hex($ciphertext));
    }

    public function decrypt(#[SensitiveParameter] string $cipherHex, #[SensitiveParameter] string $keyHex): string
    {
        $key = $this->hex($keyHex, 'key');
        $ciphertext = $this->hex($cipherHex, 'ciphertext');
        $plaintext = openssl_decrypt($ciphertext, 'AES-'.(strlen($key) * 8).'-CBC', $key, OPENSSL_RAW_DATA, self::ZERO_IV);
        if ($plaintext === false) {
            throw new ProtocolException('AES decryption failed.');
        }

        if ($plaintext === '') {
            return '';
        }

        $padding = ord($plaintext[strlen($plaintext) - 1]);

        return $padding >= 1 && $padding <= 16 ? substr($plaintext, 0, -$padding) : $plaintext;
    }

    private function hex(string $hex, string $name): string
    {
        $hex = preg_replace('/^0x/i', '', trim($hex)) ?? '';
        if ($hex === '' || strlen($hex) % 2 !== 0 || !ctype_xdigit($hex)) {
            throw new ProtocolException("Invalid AES {$name}.");
        }

        $binary = hex2bin($hex);

        return $binary === false ? throw new ProtocolException("Invalid AES {$name}.") : $binary;
    }
}
