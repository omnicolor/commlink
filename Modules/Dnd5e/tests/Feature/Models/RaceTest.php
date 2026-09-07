<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Models;

use Modules\Dnd5e\Enums\Ability;
use Modules\Dnd5e\Enums\CreatureSize;
use Modules\Dnd5e\Models\Race;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

#[Group('dnd5e')]
#[Medium]
final class RaceTest extends TestCase
{
    public function testAbilityIncreases(): void
    {
        $race = Race::findOrFail('hill-dwarf');
        self::assertSame(
            [
                Ability::Constitution->value => 2,
                Ability::Wisdom->value => 1,
            ],
            $race->ability_increases,
        );
    }

    public function testToString(): void
    {
        $race = Race::findOrFail('hill-dwarf');
        self::assertSame('Hill Dwarf', (string)$race);
    }

    public function testSize(): void
    {
        $race = Race::findOrFail('hill-dwarf');
        self::assertSame(CreatureSize::Medium, $race->size);
    }
}
