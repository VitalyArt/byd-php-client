<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

use Throwable;

/**
 * Exception related to HTTP transport.
 */
class BydTransportException extends BydException
{
    private int $statusCode;

    public function __construct(string $message, int $statusCode, private string $endpoint, ?Throwable $previous = null)
    {
        parent::__construct($message, $statusCode, $previous);
        $this->statusCode = $statusCode;
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
