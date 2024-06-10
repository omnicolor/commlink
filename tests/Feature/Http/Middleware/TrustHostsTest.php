<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\TrustHosts;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class TrustHostsTest extends TestCase
{
    public function testTrustsHosts(): void
    {
        $trustHosts = new TrustHosts($this->createApplication());
        self::assertCount(1, $trustHosts->hosts());
    }
}
