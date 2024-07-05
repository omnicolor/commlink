<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\SocialClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class SocialClassTest extends TestCase
{
    public function testLoadInvalidClass(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Social Class ID "q" is invalid');
        new SocialClass('q');
    }

    public function testLoadValidClass(): void
    {
        $class = new SocialClass('outsider');
        self::assertSame('outsider', $class->id);
        self::assertSame('Outsider', $class->name);
    }

    public function testToString(): void
    {
        $class = new SocialClass('middle');
        self::assertSame('Middle Class', (string)$class);
    }
}
