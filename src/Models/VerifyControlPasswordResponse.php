<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

class VerifyControlPasswordResponse extends BaseModel
{
    private string $vin;
    private ?bool $success = null;
    private ?string $message = null;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->vin = isset($data['vin']) ? (string) $data['vin'] : '';
        $this->success = isset($data['success']) ? (bool) $data['success'] : null;
        $this->message = isset($data['message']) ? (string) $data['message'] : null;
    }

    public function getVin(): string
    {
        return $this->vin;
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
