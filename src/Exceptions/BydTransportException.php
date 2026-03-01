<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

/**
 * Exception related to HTTP transport.
 */
class BydTransportException extends BydException
{
    private int $statusCode;
    private string $endpoint;

    public function __construct(string $message, int $statusCode, string $endpoint)
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->endpoint = $endpoint;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
