<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\TrustHosts;

/**
 * @covers \App\Http\Middleware\TrustHosts
 */
final class TrustHostsTest extends \Tests\TestCase
{
    public function testTrustsHosts(): void
    {
        $trustHosts = new TrustHosts($this->createApplication());
        self::assertCount(1, $trustHosts->hosts());
    }
}
