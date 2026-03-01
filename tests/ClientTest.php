<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests;

use Byd\ApiClient\Client;
use Byd\ApiClient\Config\BydConfig;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testClientCanBeCreated(): void
    {
        $config = new BydConfig(
            'test@example.com',
            'password123'
        );

        $client = new Client($config);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConfigCanBeAccessed(): void
    {
        $config = new BydConfig(
            'test@example.com',
            'password123',
            'https://example.com',
            'US',
            'en'
        );

        $client = new Client($config);

        $this->assertSame('test@example.com', $config->getUsername());
        $this->assertSame('password123', $config->getPassword());
        $this->assertSame('https://example.com', $config->getBaseUrl());
        $this->assertSame('US', $config->getCountryCode());
        $this->assertSame('en', $config->getLanguage());
    }
}
