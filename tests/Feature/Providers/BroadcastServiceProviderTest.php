<?php

declare(strict_types=1);

namespace Tests\Feature\Providers;

use App\Providers\BroadcastServiceProvider;

/**
 * @small
 */
final class BroadcastServiceProviderTest extends \Tests\TestCase
{
    public function testNothing(): void
    {
        $service = new BroadcastServiceProvider($this->createApplication());
        $service->boot();
        self::assertSame([], $service->provides());
    }
}
