<?php

declare(strict_types=1);

namespace Modules\Alien\Tests\Feature\Models;

use Modules\Alien\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('alien')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testToString(): void
    {
        $character = new Character(['name' => 'Bob']);
        self::assertSame('Bob', (string)$character);
    }

    public function testMaximumHealth(): void
    {
        /** @var Character */
        $character = Character::factory()->make();
        self::assertSame($character->strength, $character->health_maximum);
    }

    public function testMaximumEncumbrance(): void
    {
        /** @var Character */
        $character = Character::factory()->make();
        self::assertSame(
            2 * $character->strength,
            $character->encumbrance_maximum,
        );
    }

    public function testEncumbranceEmpty(): void
    {
        $character = new Character();
        self::assertSame(0, $character->encumbrance);
    }
}
