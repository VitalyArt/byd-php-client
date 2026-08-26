<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Crypto\Cryptography;
use DateTimeImmutable;
use SensitiveParameter;

final readonly class Session
{
    public function __construct(
        public string $userId,
        #[SensitiveParameter]
        public string $signToken,
        #[SensitiveParameter]
        public string $encryptionToken,
        public DateTimeImmutable $createdAt,
        public int $ttlSeconds,
    ) {
    }

    public function isExpired(DateTimeImmutable $now): bool
    {
        return $now->getTimestamp() >= $this->createdAt->getTimestamp() + $this->ttlSeconds;
    }

    public function signingKey(Cryptography $cryptography): string
    {
        return $cryptography->md5($this->signToken);
    }

    public function contentKey(Cryptography $cryptography): string
    {
        return $cryptography->md5($this->encryptionToken);
    }
}
