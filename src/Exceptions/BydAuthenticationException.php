<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exceptions;

/**
 * Exception related to authentication.
 */
class BydAuthenticationException extends BydException
{
    protected $code = '';
    private string $endpoint = '';

    public function __construct(string $message, string $code = '', string $endpoint = '')
    {
        parent::__construct($message);
        $this->code = $code;
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
