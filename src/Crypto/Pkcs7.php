<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use function ord;
use function strlen;

/**
 * PKCS#7 padding for the Bangcle white-box AES layer.
 *
 * Ports addPkcs7 and stripPkcs7 from bangcle.js.
 */
class Pkcs7
{
    /**
     * Add PKCS#7 padding.
     *
     * If *data* length is already a multiple of *blockSize*, a full block
     * of padding is appended (per the PKCS#7 spec).
     *
     * @param string $data Data to pad.
     * @param int $blockSize Block size in bytes (default 16).
     * @return string Padded data whose length is a multiple of *blockSize*.
     */
    public function addPkcs7(string $data, int $blockSize = 16): string
    {
        $dataLength = strlen($data);
        $remainder = $dataLength % $blockSize;
        $padLen = ($remainder === 0) ? $blockSize : ($blockSize - $remainder);

        return $data . str_repeat(pack('C', $padLen), $padLen);
    }

    /**
     * Strip PKCS#7 padding, returning data as-is if padding is invalid.
     *
     * @param string $data Potentially padded data.
     * @return string Unpadded data, or the original *data* if padding is invalid.
     */
    public function stripPkcs7(string $data): string
    {
        $dataLength = strlen($data);

        if ($dataLength === 0) {
            return $data;
        }

        $pad = ord($data[$dataLength - 1]);

        if ($pad === 0 || $pad > 16) {
            return $data;
        }

        if ($dataLength < $pad) {
            return $data;
        }

        // Check if all padding bytes are correct
        for ($i = $dataLength - $pad; $i < $dataLength; $i++) {
            if (ord($data[$i]) !== $pad) {
                return $data;
            }
        }

        return substr($data, 0, $dataLength - $pad);
    }
}
