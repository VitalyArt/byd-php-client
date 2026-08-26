<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Byd\ApiClient\Contract\SleeperInterface;
use Byd\ApiClient\Policy\PollingPolicy;
use Psr\Clock\ClockInterface;

final readonly class PollingExecutor
{
    public function __construct(private PollingPolicy $policy, private ClockInterface $clock, private SleeperInterface $sleeper)
    {
    }

    /**
     * @template T
     * @param callable(): T $fetch
     * @param callable(T): bool $ready
     * @return T
     */
    public function until(callable $fetch, callable $ready): mixed
    {
        $started = $this->clock->now();
        $latest = $fetch();
        for ($attempt = 1; !$ready($latest) && $attempt < $this->policy->maximumAttempts; ++$attempt) {
            $elapsed = ($this->clock->now()->getTimestamp() - $started->getTimestamp()) * 1000;
            if ($elapsed >= $this->policy->timeoutMilliseconds) {
                break;
            }

            $this->sleeper->sleep($this->policy->intervalMilliseconds);
            $latest = $fetch();
        }

        return $latest;
    }
}
