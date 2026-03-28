<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Token returned after successful login.
 */
class AuthToken extends BaseModel
{
    private string $userId;

    private string $signToken;

    private string $encryToken;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->userId = (string) ($data['userId'] ?? '');
        $this->signToken = (string) ($data['signToken'] ?? '');
        $this->encryToken = (string) ($data['encryToken'] ?? '');
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
}
