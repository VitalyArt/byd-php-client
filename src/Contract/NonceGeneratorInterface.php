<?php

declare(strict_types=1);

namespace Byd\ApiClient\Contract;

interface NonceGeneratorInterface
{
    public function generate(): string;
}
