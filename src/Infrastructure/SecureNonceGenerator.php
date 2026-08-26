<?php

declare(strict_types=1);

namespace Byd\ApiClient\Infrastructure;

use Byd\ApiClient\Contract\NonceGeneratorInterface;

final class SecureNonceGenerator implements NonceGeneratorInterface
{
    public function generate(): string
    {
        return strtoupper(bin2hex(random_bytes(16)));
    }
}
