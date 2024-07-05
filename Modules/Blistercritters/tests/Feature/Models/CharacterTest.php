<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Tests\Feature\Models;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Blistercritters\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('blistercritters')]
#[Small]
class CharacterTest extends TestCase
{
    use WithFaker;

    public function testToString(): void
    {
        $name = $this->faker->name;
        $character = new Character(['name' => $name]);
        self::assertSame($name, (string)$character);
    }

    public function testToStringUnnamed(): void
    {
        $character = new Character();
        self::assertSame('Unnamed Critter', (string)$character);
    }

    public function testHealth(): void
    {
        $character = new Character(['scrap' => 10, 'scurry' => 8]);
        self::assertSame(18, $character->starting_health);
        $character = new Character(['scrap' => 6, 'scurry' => 6]);
        self::assertSame(12, $character->starting_health);
    }
}
