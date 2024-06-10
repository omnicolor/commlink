<?php

declare(strict_types=1);

namespace Tests\Feature\Providers;

use App\Providers\BroadcastServiceProvider;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class BroadcastServiceProviderTest extends TestCase
{
    public function testNothing(): void
    {
        $service = new BroadcastServiceProvider($this->createApplication());
        $service->boot();
        self::assertSame([], $service->provides());
    }
}
