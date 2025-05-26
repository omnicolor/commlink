<?php

declare(strict_types=1);

namespace Tests\Feature\Casts;

use App\Casts\AsEmail;
use App\Models\User;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class AsEmailTest extends TestCase
{
    public function testInvalidGet(): void
    {
        $cast = new AsEmail();
        self::expectException(InvalidArgumentException::class);
        $cast->get(new User(), 'email', 'bob', []);
    }

    public function testValidGet(): void
    {
        $cast = new AsEmail();
        $email = $cast->get(new User(), 'email', 'bob@example.com', []);
        self::assertSame('bob@example.com', $email->address);
    }
}
