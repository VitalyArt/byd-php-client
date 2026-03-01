<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

use Exception;
use Throwable;

class BydApiException extends Exception
{
    private string $endpoint = '';

    public function __construct(string $message, int $code = 0, string $endpoint = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
