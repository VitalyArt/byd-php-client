<?php

declare(strict_types=1);

namespace Byd\ApiClient\Contract;

interface SleeperInterface
{
    public function sleep(int $milliseconds): void;
}
