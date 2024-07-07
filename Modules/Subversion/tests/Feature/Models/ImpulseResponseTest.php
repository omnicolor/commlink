<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Modules\Subversion\Models\ImpulseResponse;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('subversion')]
#[Small]
final class ImpulseResponseTest extends TestCase
{
    public function testToString(): void
    {
        $impulseResponse = new ImpulseResponse('id', 'name', 'description', ['effect' => 1]);
        self::assertSame('name', (string)$impulseResponse);
    }
}
