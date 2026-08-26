<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use function ord;
use function sprintf;
use function strlen;

/**
 * Hashing utilities.
 */
class Hashing
{
    /**
     * Compute MD5 of a UTF-8 string, returning uppercase hex.
     *
     * Mirrors JS: crypto.createHash('md5').update(value, 'utf8').digest('hex').toUpperCase()
     */
    public static function md5Hex(string $input): string
    {
        return strtoupper(md5($input));
    }

    /**
     * Derive the login AES key from a plaintext password.
     *
     * Mirrors JS: md5Hex(md5Hex(password))
     */
    public static function pwdLoginKey(string $password): string
    {
        return self::md5Hex(self::md5Hex($password));
    }

    /**
     * Compute SHA1 with alternating-case hex and zero filtering.
     *
     * Algorithm (from client.js lines 58-76):
     *   1. SHA1 digest of UTF-8 encoded value -> 20 bytes
     *   2. For each byte at index *i*, format as 2-char hex:
     *      - Even *i*: uppercase
     *      - Odd *i*: lowercase
     *   3. Concatenate into a 40-char string
     *   4. Filter: drop any '0' character that falls at an even position
     */
    public static function sha1Mixed(string $input): string
    {
        $digest = sha1($input, true); // Get raw binary output
        $mixedChars = [];

        for ($i = 0; $i < strlen($digest); $i++) {
            $byteVal = ord($digest[$i]);
            $hexStr = sprintf('%02x', $byteVal);

            $mixedChars[] = $i % 2 === 0 ? strtoupper($hexStr) : strtolower($hexStr);
        }

        $mixed = implode('', $mixedChars);
        $filtered = '';

        for ($j = 0; $j < strlen($mixed); $j++) {
            $ch = $mixed[$j];
            if ($ch === '0' && $j % 2 === 0) {
                continue;
            }

            $filtered .= $ch;
        }

        return $filtered;
    }

    /**
     * Compute checkcode: MD5 of compact JSON with chunk reordering.
     *
     * The MD5 hex digest is reordered as:
     *   [24:32] + [8:16] + [16:24] + [0:8]
     */
    public static function computeCheckcode(array $payload): string
    {
        // Python's json.dumps preserves insertion order. The API includes
        // that exact order in the checkcode input, so do not sort here.
        $jsonStr = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        $md5 = md5($jsonStr);

        return substr($md5, 24, 8) . substr($md5, 8, 8) . substr($md5, 16, 8) . substr($md5, 0, 8);
    }
}
