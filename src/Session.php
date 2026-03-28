<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Crypto\Hashing;

/**
 * Mutable session state after successful login.
 */
class Session
{
    private float $createdAt;

    private ?string $contentKeyCache = null;

    private ?string $signKeyCache = null;

    public function __construct(
        private string $userId,
        private string $signToken,
        private string $encryToken,
        private float $ttl = 43200.0, // 12 hours default
        ?float $createdAt = null
    ) {
        $this->createdAt = $createdAt ?? microtime(true);
    }

    /**
     * AES key for encrypting/decrypting inner payload data.
     * Derived as MD5(encry_token) in uppercase hex.
     */
    public function contentKey(): string
    {
        if ($this->contentKeyCache === null) {
            $this->contentKeyCache = Hashing::md5Hex($this->encryToken);
        }

        return $this->contentKeyCache;
    }

    /**
     * Key used in request signature computation.
     * Derived as MD5(sign_token) in uppercase hex.
     */
    public function signKey(): string
    {
        if ($this->signKeyCache === null) {
            $this->signKeyCache = Hashing::md5Hex($this->signToken);
        }

        return $this->signKeyCache;
    }

    /**
     * Whether the session has exceeded its TTL.
     */
    public function isExpired(): bool
    {
        return (microtime(true) - $this->createdAt) >= $this->ttl;
    }

    /**
     * Seconds since the session was created.
     */
    public function age(): float
    {
        return microtime(true) - $this->createdAt;
    }

    // Getters
    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getSignToken(): string
    {
        return $this->signToken;
    }

    public function getEncryToken(): string
    {
        return $this->encryToken;
    }

    public function getCreatedAt(): float
    {
        return $this->createdAt;
    }

    public function getTtl(): float
    {
        return $this->ttl;
    }
}
