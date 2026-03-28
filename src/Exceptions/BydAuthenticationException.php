<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

/**
 * Exception related to authentication.
 */
class BydAuthenticationException extends BydException
{
    protected $code = '';

    public function __construct(string $message, string $code = '', private string $endpoint = '')
    {
        parent::__construct($message);
        $this->code = $code;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
