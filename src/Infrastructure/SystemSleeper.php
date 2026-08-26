<?php

declare(strict_types=1);

namespace Byd\ApiClient\Infrastructure;

use Byd\ApiClient\Contract\SleeperInterface;

final class SystemSleeper implements SleeperInterface
{
    public function sleep(int $milliseconds): void
    {
        if ($milliseconds > 0) {
            usleep($milliseconds * 1000);
        }
    }
}
