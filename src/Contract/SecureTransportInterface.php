<?php

declare(strict_types=1);

namespace Byd\ApiClient\Contract;

use Byd\ApiClient\Enum\Endpoint;

interface SecureTransportInterface
{
    /** @return array<string, mixed> */
    public function send(Endpoint $endpoint, object $request): array;
}
