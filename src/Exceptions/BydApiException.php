<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

use Throwable;

class BydApiException extends BydException
{
    public function __construct(string $message, int $code = 0, private string $endpoint = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
