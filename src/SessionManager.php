<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use Psr\Clock\ClockInterface;

final class SessionManager
{
    private ?Session $session = null;

    public function __construct(private readonly AuthenticationService $authentication, private readonly ClockInterface $clock)
    {
    }

    public function current(): Session
    {
        if (!$this->session instanceof Session || $this->session->isExpired($this->clock->now())) {
            $this->session = $this->authentication->authenticate();
        }

        return $this->session;
    }

    public function refresh(): Session
    {
        return $this->session = $this->authentication->authenticate();
    }

    public function invalidate(): void
    {
        $this->session = null;
    }
}
