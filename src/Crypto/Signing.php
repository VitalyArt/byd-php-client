<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

/**
 * Request signing for BYD API.
 */
class Signing
{
    /**
     * Build the sign string by sorting fields and appending password.
     *
     * Algorithm (from client.js lines 78-82):
     *   1. Sort field keys alphabetically
     *   2. Join as ``key=value`` pairs with ``&``
     *   3. Append ``&password=<password>``
     *
     * @param array<string, string|null> $fields
     */
    public static function buildSignString(array $fields, string $password): string
    {
        ksort($fields);
        $parts = [];

        foreach ($fields as $key => $value) {
            $parts[] = $key . '=' . ($value ?? 'null');
        }

        $joined = implode('&', $parts);

        return $joined . '&password=' . $password;
    }
}
