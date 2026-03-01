<?php

declare(strict_types=1);

namespace Byd\ApiClient\Crypto;

use Byd\ApiClient\Exceptions\BangcleException;

use function sprintf;
use function strlen;

/**
 * Encode and decode Bangcle envelopes using white-box AES.
 */
class BangcleCodec
{
    private ?string $tablesPath;
    private ?array $tables = null;

    // Binary table file format constants
    private const MAGIC = 'BGTB';
    private const VERSION = 1;
    private const TABLE_COUNT = 8;
    private const HEADER_SIZE = 4 + 2 + 2; // magic + version + count
    private const INDEX_ENTRY_SIZE = 4 + 4; // offset + length
    private const INDEX_SIZE = self::TABLE_COUNT * self::INDEX_ENTRY_SIZE;
    private const ZERO_IV = "\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00\x00";

    // Expected sizes for each table, in order.
    private const TABLE_SPECS = [
        ['inv_round', 0x28000],
        ['inv_xor', 0x3C000],
        ['inv_first', 0x1000],
        ['round', 0x28000],
        ['xor', 0x3C000],
        ['final', 0x1000],
        ['perm_decrypt', 8],
        ['perm_encrypt', 8],
    ];

    public function __construct(?string $tablesPath = null)
    {
        $this->tablesPath = $tablesPath;
    }

    /**
     * Load tables from binary file.
     *
     * @throws BangcleException
     */
    private function loadTables(): array
    {
        if ($this->tables !== null) {
            return $this->tables;
        }

        if ($this->tablesPath !== null) {
            if (!file_exists($this->tablesPath)) {
                throw new BangcleException('Table file not found: ' . $this->tablesPath);
            }
            $raw = file_get_contents($this->tablesPath);
            if ($raw === false) {
                throw new BangcleException('Failed to read table file: ' . $this->tablesPath);
            }
        } else {
            // Try to find the tables file in the package data directory
            $defaultPath = __DIR__ . '/../data/bangcle_tables.bin';
            if (file_exists($defaultPath)) {
                $raw = file_get_contents($defaultPath);
                if ($raw === false) {
                    throw new BangcleException('Failed to read default table file');
                }
            } else {
                throw new BangcleException(
                    'bangcle_tables.bin not found. Please provide the path to the tables file.'
                );
            }
        }

        $this->tables = $this->parseTables($raw);

        return $this->tables;
    }

    /**
     * Parse the binary table file.
     *
     * @throws BangcleException
     *
     * @return array<string, string>
     */
    private function parseTables(string $data): array
    {
        $dataLength = strlen($data);
        if ($dataLength < self::HEADER_SIZE + self::INDEX_SIZE) {
            throw new BangcleException('Table file too short');
        }

        $magic = substr($data, 0, 4);
        if ($magic !== self::MAGIC) {
            throw new BangcleException(sprintf('Bad magic: expected %s, got %s', self::MAGIC, bin2hex($magic)));
        }

        $version = unpack('v', substr($data, 4, 2))[1];
        if ($version !== self::VERSION) {
            throw new BangcleException(sprintf('Unsupported table version: %d', $version));
        }

        $count = unpack('v', substr($data, 6, 2))[1];
        if ($count !== self::TABLE_COUNT) {
            throw new BangcleException(sprintf('Expected %d tables, got %d', self::TABLE_COUNT, $count));
        }

        $tables = [];

        // Parse index entries to get table offsets and lengths
        for ($i = 0; $i < self::TABLE_COUNT; $i++) {
            // Read offset and length from index
            $idxOffset = self::HEADER_SIZE + $i * self::INDEX_ENTRY_SIZE;
            $indexData = unpack('Voffset/Vlength', substr($data, $idxOffset, self::INDEX_ENTRY_SIZE));
            $tableOffset = $indexData['offset'];
            $expectedLen = $indexData['length'];

            $expectedName = self::TABLE_SPECS[$i][0];
            $expectedLength = self::TABLE_SPECS[$i][1];

            if ($expectedLen !== $expectedLength) {
                throw new BangcleException(sprintf('Table %s: expected %d bytes, got %d', $expectedName, $expectedLength, $expectedLen));
            }
            if ($tableOffset + $expectedLen > $dataLength) {
                throw new BangcleException(sprintf('Table %s: data extends beyond file', $expectedName));
            }

            $tables[$expectedName] = substr($data, $tableOffset, $expectedLen);
        }

        return $tables;
    }

    /**
     * Normalize a Bangcle envelope string for decoding.
     *
     * @throws BangcleException
     */
    private function normalizeEnvelopeInput(string $envelope): string
    {
        $cleaned = trim(str_replace([' ', "\t", "\n", "\r"], '', $envelope));

        // URL-safe base64 normalization
        $cleaned = str_replace(['-', '_'], ['+', '/'], $cleaned);

        if ($cleaned === '') {
            throw new BangcleException('Bangcle input is empty');
        }

        if (!str_starts_with($cleaned, 'F')) {
            throw new BangcleException('Bangcle envelope must start with "F"');
        }

        $cleaned = substr($cleaned, 1); // strip F prefix
        $remainder = strlen($cleaned) % 4;
        if ($remainder !== 0) {
            $cleaned .= str_repeat('=', 4 - $remainder);
        }

        return $cleaned;
    }

    /**
     * Encode plaintext into a Bangcle envelope (F + base64).
     *
     * @param string $plaintext Data to encode.
     * @return string Envelope string starting with "F".
     * @throws BangcleException
     */
    public function encodeEnvelope(string $plaintext): string
    {
        $tables = $this->loadTables();
        $plainBytes = $plaintext; // In PHP strings are bytes
        $padded = Pkcs7::addPkcs7($plainBytes);
        $ciphertext = BangcleBlock::encryptCbc($tables, $padded, self::ZERO_IV);

        return 'F' . base64_encode($ciphertext);
    }

    /**
     * Decode a Bangcle envelope back to plaintext.
     *
     * @param string $envelope Envelope string (must start with "F").
     * @return string Decoded plaintext.
     * @throws BangcleException
     */
    public function decodeEnvelope(string $envelope): string
    {
        $tables = $this->loadTables();
        $b64Payload = $this->normalizeEnvelopeInput($envelope);

        $ciphertext = base64_decode($b64Payload, true);
        if ($ciphertext === false) {
            throw new BangcleException('Invalid base64 in Bangcle envelope');
        }

        if ($ciphertext === '') {
            throw new BangcleException('Bangcle ciphertext is empty');
        }

        $cipherLength = strlen($ciphertext);
        if ($cipherLength % 16 !== 0) {
            throw new BangcleException(sprintf('Bangcle ciphertext length %d is not a multiple of 16', $cipherLength));
        }

        $plaintext = BangcleBlock::decryptCbc($tables, $ciphertext, self::ZERO_IV);

        return Pkcs7::stripPkcs7($plaintext);
    }
}
