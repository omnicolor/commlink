<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\ImpulseResponse;
use PHPUnit\Framework\TestCase;

/**
 * @group subversion
 * @small
 */
final class ImpulseResponseTest extends TestCase
{
    public function testToString(): void
    {
        $impulseResponse = new ImpulseResponse('id', 'name', 'description', ['effect' => 1]);
        self::assertSame('name', (string)$impulseResponse);
    }
}
