<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use Byd\ApiClient\Exceptions\BangcleException;

use function ord;
use function strlen;

/**
 * White-box AES block cipher and CBC mode for Bangcle envelopes.
 *
 * Faithful port of bangcle.js: decryptBlockAuth, encryptBlockAuth,
 * decryptCbc, encryptCbc.
 *
 * The cipher uses pre-computed lookup tables extracted from
 * libencrypt.so rather than a standard AES key schedule.
 */
class BangcleBlock
{
    /**
     * Transpose 4x4 block into working state layout (col*8+row).
     */
    private static function prepareAesMatrix(string $inputBlock, array &$output): void
    {
        for ($col = 0; $col < 4; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $output[$col * 8 + $row] = ord($inputBlock[$col + $row * 4]);
            }
        }
    }

    /**
     * Decrypt a single 16-byte block using white-box AES tables.
     *
     * Port of bangcle.js decryptBlockAuth (lines 37-144).
     *
     * @param array $tables Pre-loaded lookup tables.
     * @param string $block 16-byte ciphertext block.
     * @param int $roundStart Starting round (default 1 = full decryption).
     * @return string 16-byte decrypted block.
     * @throws BangcleException
     */
    public static function decryptBlockAuth(array $tables, string $block, int $roundStart = 1): string
    {
        $state = array_fill(0, 32, 0);
        $temp64 = array_fill(0, 64, 0);
        $tmp32 = array_fill(0, 32, 0);
        $output = array_fill(0, 16, 0);

        self::prepareAesMatrix($block, $state);
        $param3 = $roundStart;

        for ($rnd = 9; $rnd > max(0, $param3 - 1); $rnd--) {
            $l_var20 = $rnd;
            $l_var21 = $l_var20 * 4;
            $permPtr = 0;

            for ($i = 0; $i < 4; $i++) {
                $b_var3 = ord($tables['perm_decrypt'][$permPtr]);
                $l_var16 = $i * 8;
                $base = $i * 16;

                for ($j = 0; $j < 4; $j++) {
                    $u_var7 = ($b_var3 + $j) & 3;
                    $byteVal = $state[$l_var16 + $u_var7];
                    $idx = $byteVal + ($i + ($l_var21 + $u_var7) * 4) * 256;

                    // Extract 4 bytes as little-endian integer from inv_round table
                    $value = self::unpackLittleEndianInt(substr((string) $tables['inv_round'], $idx * 4, 4));
                    self::packLittleEndianInt($value, $temp64, $base + $j * 4);
                }

                $permPtr += 2;
            }

            $i_var15 = 1;
            for ($l_var21_xor = 0; $l_var21_xor < 4; $l_var21_xor++) {
                $pb_var18_offset = $l_var21_xor;

                for ($l_var9_xor = 0; $l_var9_xor < 4; $l_var9_xor++) {
                    $local10 = $temp64[$pb_var18_offset];
                    $u_var6 = $local10 & 0xF;
                    $u_var26 = $local10 & 0xF0;

                    $local_f0 = $temp64[$pb_var18_offset + 0x10];
                    $local_f1 = $temp64[$pb_var18_offset + 0x20];
                    $local_f2 = $temp64[$pb_var18_offset + 0x30];

                    $l_var2 = $l_var9_xor * 0x18 + $l_var20 * 0x60;
                    $i_var25 = $i_var15;

                    for ($l_var16 = 0; $l_var16 < 3; $l_var16++) {
                        if ($l_var16 === 0) {
                            $b_var3_inner = $local_f0;
                        } elseif ($l_var16 === 1) {
                            $b_var3_inner = $local_f1;
                        } else {
                            $b_var3_inner = $local_f2;
                        }

                        $u_var1 = ($b_var3_inner << 4) & 0xFF;
                        $u_var27 = $u_var6 | $u_var1;
                        $u_var26 = (($u_var26 >> 4) | (($b_var3_inner >> 4) << 4)) & 0xFF;

                        $idx1 = ($l_var2 + ($i_var25 - 1)) * 0x100 + $u_var27;
                        $u_var6 = ord($tables['inv_xor'][$idx1]) & 0xF;

                        $idx2 = ($l_var2 + $i_var25) * 0x100 + $u_var26;
                        $b_var3_new = ord($tables['inv_xor'][$idx2]);
                        $u_var26 = ($b_var3_new & 0xF) << 4;
                        $i_var25 += 2;
                    }

                    $state[$l_var9_xor + $l_var21_xor * 8] = ($u_var26 | $u_var6) & 0xFF;
                    $pb_var18_offset += 4;
                }

                $i_var15 += 6;
            }
        }

        if ($param3 === 1) {
            $tmp32 = $state;
            $u_var8 = 1;
            $u_var10 = 3;
            $u_var12 = 2;

            for ($row = 0; $row < 4; $row++) {
                $idx0 = $tmp32[$row] + $row * 0x400;
                $state[$row] = ord($tables['inv_first'][$idx0]);

                $row1 = $u_var10 & 3;
                $idx1 = $tmp32[8 + $row1] + $row1 * 0x400 + 0x100;
                $state[8 + $row] = ord($tables['inv_first'][$idx1]);

                $row2 = $u_var12 & 3;
                $idx2 = $tmp32[0x10 + $row2] + $row2 * 0x400 + 0x200;
                $state[0x10 + $row] = ord($tables['inv_first'][$idx2]);

                $row3 = $u_var8 & 3;
                $idx3 = $tmp32[0x18 + $row3] + $row3 * 0x400 + 0x300;
                $state[0x18 + $row] = ord($tables['inv_first'][$idx3]);

                $u_var8 += 1;
                $u_var10 += 1;
                $u_var12 += 1;
            }
        }

        for ($col = 0; $col < 4; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $output[$col + $row * 4] = $state[$col * 8 + $row];
            }
        }

        return implode('', array_map(chr(...), $output));
    }

    /**
     * Encrypt a single 16-byte block using white-box AES tables.
     *
     * Port of bangcle.js encryptBlockAuth (lines 147-253).
     *
     * @param array $tables Pre-loaded lookup tables.
     * @param string $block 16-byte plaintext block.
     * @param int $roundEnd Ending round (default 10 = full encryption).
     * @return string 16-byte encrypted block.
     * @throws BangcleException
     */
    public static function encryptBlockAuth(array $tables, string $block, int $roundEnd = 10): string
    {
        $state = array_fill(0, 32, 0);
        $temp64 = array_fill(0, 64, 0);
        $tmp32 = array_fill(0, 32, 0);
        $output = array_fill(0, 16, 0);

        self::prepareAesMatrix($block, $state);
        $param3 = $roundEnd;

        $rounds = min(9, max(0, $param3));
        for ($rnd = 0; $rnd < $rounds; $rnd++) {
            $l_var21 = $rnd * 4;
            $permPtr = 0;

            for ($i = 0; $i < 4; $i++) {
                $b_var4 = ord($tables['perm_encrypt'][$permPtr]);
                $l_var16 = $i * 8;
                $base = $i * 16;

                for ($j = 0; $j < 4; $j++) {
                    $u_var8 = ($b_var4 + $j) & 3;
                    $byteVal = $state[$l_var16 + $u_var8];
                    $idx = $byteVal + ($i + ($l_var21 + $u_var8) * 4) * 256;

                    // Extract 4 bytes as little-endian integer from round table
                    $value = self::unpackLittleEndianInt(substr((string) $tables['round'], $idx * 4, 4));
                    self::packLittleEndianInt($value, $temp64, $base + $j * 4);
                }

                $permPtr += 2;
            }

            $i_var16 = 1;
            for ($l_var22 = 0; $l_var22 < 4; $l_var22++) {
                $pb_var19_offset = $l_var22;

                for ($l_var10 = 0; $l_var10 < 4; $l_var10++) {
                    $local10 = $temp64[$pb_var19_offset];
                    $u_var7 = $local10 & 0xF;
                    $u_var26 = $local10 & 0xF0;

                    $local_f0 = $temp64[$pb_var19_offset + 0x10];
                    $local_f1 = $temp64[$pb_var19_offset + 0x20];
                    $local_f2 = $temp64[$pb_var19_offset + 0x30];

                    $l_var2 = $l_var10 * 0x18 + $rnd * 0x60;
                    $i_var25 = $i_var16;

                    for ($l_var17 = 0; $l_var17 < 3; $l_var17++) {
                        if ($l_var17 === 0) {
                            $b_var4_inner = $local_f0;
                        } elseif ($l_var17 === 1) {
                            $b_var4_inner = $local_f1;
                        } else {
                            $b_var4_inner = $local_f2;
                        }

                        $u_var1 = ($b_var4_inner << 4) & 0xFF;
                        $u_var27 = $u_var7 | $u_var1;
                        $u_var26 = (($u_var26 >> 4) | (($b_var4_inner >> 4) << 4)) & 0xFF;

                        $idx1 = ($l_var2 + ($i_var25 - 1)) * 0x100 + $u_var27;
                        $u_var7 = ord($tables['xor'][$idx1]) & 0xF;

                        $idx2 = ($l_var2 + $i_var25) * 0x100 + $u_var26;
                        $b_var4_new = ord($tables['xor'][$idx2]);
                        $u_var26 = ($b_var4_new & 0xF) << 4;
                        $i_var25 += 2;
                    }

                    $state[$l_var10 + $l_var22 * 8] = ($u_var26 | $u_var7) & 0xFF;
                    $pb_var19_offset += 4;
                }

                $i_var16 += 6;
            }
        }

        if ($param3 === 10) {
            $tmp32 = $state;
            $u_var13 = 3;
            $u_var9 = 2;
            $u_var11 = 1;
            $u_var8_enc = 0;

            for ($row = 0; $row < 4; $row++) {
                $row0 = ($u_var8_enc + $row) & 3;
                $state[$row] = ord($tables['final'][$tmp32[$row0] + $row0 * 0x400]);

                $row1 = ($u_var11 + $row) & 3;
                $state[8 + $row] = ord($tables['final'][$tmp32[8 + $row1] + $row1 * 0x400 + 0x100]);

                $row2 = ($u_var9 + $row) & 3;
                $state[0x10 + $row] = ord($tables['final'][$tmp32[0x10 + $row2] + $row2 * 0x400 + 0x200]);

                $row3 = ($u_var13 + $row) & 3;
                $state[0x18 + $row] = ord($tables['final'][$tmp32[0x18 + $row3] + $row3 * 0x400 + 0x300]);
            }
        }

        for ($col = 0; $col < 4; $col++) {
            for ($row = 0; $row < 4; $row++) {
                $output[$col + $row * 4] = $state[$col * 8 + $row];
            }
        }

        return implode('', array_map(chr(...), $output));
    }

    /**
     * Decrypt data using white-box AES in CBC mode.
     *
     * @param array $tables Pre-loaded lookup tables.
     * @param string $data Ciphertext (must be a multiple of 16 bytes).
     * @param string $iv 16-byte initialization vector.
     * @return string Decrypted plaintext.
     * @throws BangcleException
     */
    public static function decryptCbc(array $tables, string $data, string $iv): string
    {
        $dataLength = strlen($data);
        $ivLength = strlen($iv);

        if ($dataLength % 16 !== 0) {
            throw new BangcleException("Ciphertext length {$dataLength} is not a multiple of 16");
        }

        if ($ivLength !== 16) {
            throw new BangcleException("IV must be 16 bytes, got {$ivLength}");
        }

        $result = '';
        $prev = $iv;

        for ($offset = 0; $offset < $dataLength; $offset += 16) {
            $block = substr($data, $offset, 16);
            $decrypted = self::decryptBlockAuth($tables, $block, 1);

            // XOR with previous block
            $decryptedBytes = array_values(unpack('C*', $decrypted));
            $prevBytes = array_values(unpack('C*', $prev));
            for ($i = 0; $i < 16; $i++) {
                $decryptedBytes[$i] ^= $prevBytes[$i];
            }

            $result .= implode('', array_map(chr(...), $decryptedBytes));
            $prev = $block;
        }

        return $result;
    }

    /**
     * Encrypt data using white-box AES in CBC mode.
     *
     * @param array $tables Pre-loaded lookup tables.
     * @param string $data Plaintext (must be a multiple of 16 bytes).
     * @param string $iv 16-byte initialization vector.
     * @return string Ciphertext.
     * @throws BangcleException
     */
    public static function encryptCbc(array $tables, string $data, string $iv): string
    {
        $dataLength = strlen($data);
        $ivLength = strlen($iv);

        if ($dataLength % 16 !== 0) {
            throw new BangcleException("Plaintext length {$dataLength} is not a multiple of 16");
        }

        if ($ivLength !== 16) {
            throw new BangcleException("IV must be 16 bytes, got {$ivLength}");
        }

        $result = '';
        $prev = $iv;

        for ($offset = 0; $offset < $dataLength; $offset += 16) {
            $block = substr($data, $offset, 16);

            // XOR with previous block
            $blockBytes = array_values(unpack('C*', $block));
            $prevBytes = array_values(unpack('C*', $prev));
            for ($i = 0; $i < 16; $i++) {
                $blockBytes[$i] ^= $prevBytes[$i];
            }

            $block = implode('', array_map(chr(...), $blockBytes));
            $encrypted = self::encryptBlockAuth($tables, $block, 10);
            $result .= $encrypted;
            $prev = $encrypted;
        }

        return $result;
    }

    /**
     * Unpack little-endian integer from 4 bytes
     */
    private static function unpackLittleEndianInt(string $bytes): int
    {
        $unpacked = unpack('V', $bytes);

        return $unpacked[1];
    }

    /**
     * Pack integer as little-endian into array
     */
    private static function packLittleEndianInt(int $value, array &$array, int $offset): void
    {
        $packed = pack('V', $value);
        $bytes = array_values(unpack('C*', $packed));
        for ($i = 0; $i < 4; $i++) {
            $array[$offset + $i] = $bytes[$i];
        }
    }
}
