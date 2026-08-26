<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\Contract\SleeperInterface;
use Byd\ApiClient\Policy\PollingPolicy;
use Byd\ApiClient\PollingExecutor;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;
use RuntimeException;

final class PollingTest extends TestCase
{
    public function testPollingUsesInjectedPolicyClockAndSleeper(): void
    {
        $clock = new class () implements ClockInterface {
            public function now(): DateTimeImmutable
            {
                return new DateTimeImmutable('2026-01-01T00:00:00Z');
            }
        };
        $sleeper = new class () implements SleeperInterface {
            public int $calls = 0;

            public function sleep(int $milliseconds): void
            {
                if ($milliseconds !== 25) {
                    throw new RuntimeException('Unexpected polling interval.');
                }

                ++$this->calls;
            }
        };
        $executor = new PollingExecutor(new PollingPolicy(3, 25, 1000), $clock, $sleeper);
        $value = 0;

        $result = $executor->until(static function () use (&$value): int {
            return ++$value;
        }, static fn (int $latest): bool => $latest === 3);

        self::assertSame(3, $result);
        self::assertSame(2, $sleeper->calls);
    }
}
