<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/** Terminal response from BYD smart-charge changeResult. */
class ChargeChangeResult extends BaseModel
{
    private ?int $res = null;
    private ?string $message = null;
    private ?string $requestSerial = null;

    /** @param array<string, mixed> $data */
    protected function populate(array $data): void
    {
        $this->res = isset($data['res']) ? (int) $data['res'] : null;
        $this->message = isset($data['message']) ? (string) $data['message'] : (isset($data['msg']) ? (string) $data['msg'] : null);
        $this->requestSerial = isset($data['requestSerial']) ? (string) $data['requestSerial'] : null;
    }

    public function getRes(): ?int
    {
        return $this->res;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getRequestSerial(): ?string
    {
        return $this->requestSerial;
    }

    public function isSuccess(): bool
    {
        return $this->res === 2;
    }
}
