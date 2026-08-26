<?php

declare(strict_types=1);

namespace Byd\ApiClient\Policy;

use Byd\ApiClient\Exception\ValidationException;

final readonly class AuthenticationRetryPolicy
{
    public function __construct(public int $maximumReauthentications = 1)
    {
        if ($maximumReauthentications < 0 || $maximumReauthentications > 3) {
            throw new ValidationException('Reauthentication attempts must be between 0 and 3.');
        }
    }
}
