<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Character;
use App\Models\Transformers\Programming;
use Tests\TestCase;
use ValueError;

/**
 * Unit tests for Transformers characters.
 * @group models
 * @group transformers
 * @small
 */
final class CharacterTest extends TestCase
{
    public function testToString(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
        $character->name = 'Bumblebee';
        self::assertSame('Bumblebee', (string)$character);
    }

    public function testProgramming(): void
    {
        $character = new Character(['programming' => 'warrior']);
        self::assertSame(Programming::Warrior, $character->programming);
        $character->programming = Programming::Engineer;
        self::assertSame(Programming::Engineer, $character->programming);
        $character->programming = 'scout';
        self::assertSame(Programming::Scout, $character->programming);
    }

    public function testSetProgrammingInvalid(): void
    {
        self::expectException(ValueError::class);
        new Character(['programming' => 'invalid']);
    }
}
