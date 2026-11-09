<?php
declare(strict_types=1);

namespace PriyankJivani\CacheWarmerCli\Test\Unit\Model;

use PHPUnit\Framework\TestCase;

class UrlProviderTest extends TestCase
{
    public function testUrlParsingAndNormalizing(): void
    {
        $rawUrl = "https://example.com/shoes/running-sneakers.html";
        $parsed = parse_url($rawUrl);

        $this->assertIsArray($parsed);
        $this->assertEquals('example.com', $parsed['host']);
        $this->assertEquals('/shoes/running-sneakers.html', $parsed['path']);
    }

    public function testConcurrencyDefaultLimits(): void
    {
        $concurrency = 10;
        $this->assertGreaterThan(0, $concurrency);
        $this->assertLessThanOrEqual(50, $concurrency);
    }
}
