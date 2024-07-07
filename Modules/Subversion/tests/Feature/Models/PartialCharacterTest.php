<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use Illuminate\Support\Str;
use Modules\Subversion\Models\Caste;
use Modules\Subversion\Models\PartialCharacter;
use Modules\Subversion\Models\Relation;
use Modules\Subversion\Models\RelationArchetype;
use Modules\Subversion\Models\RelationAspect;
use Modules\Subversion\Models\RelationLevel;
use Modules\Subversion\Models\Skill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('subversion')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testGetFortuneBrandNew(): void
    {
        $character = new PartialCharacter();
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
    }

    public function testGetFortuneWithCaste(): void
    {
        $character = new PartialCharacter(['caste' => 'undercity']);
        $undercity = new Caste('undercity');
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + $undercity->fortune,
            $character->fortune,
        );

        $elite = new Caste('elite');
        $character->caste = $elite;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + $elite->fortune,
            $character->fortune,
        );
    }

    public function testGetFortuneWithCorruptedValue(): void
    {
        $character = new PartialCharacter();
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
        $character->corrupted_value = false;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE,
            $character->fortune,
        );
        $character->corrupted_value = true;
        self::assertSame(
            PartialCharacter::STARTING_FORTUNE + PartialCharacter::CORRUPTED_VALUE_FORTUNE,
            $character->fortune,
        );
    }

    public function testGetRelationsEmpty(): void
    {
        $character = new PartialCharacter();
        self::assertCount(0, $character->relations);
    }

    public function testGetRelations(): void
    {
        $character = new PartialCharacter([
            'relations' => [
                [
                    'name' => 'Test Relation',
                    'skill' => 'influence',
                    'archetype' => 'care',
                    'level' => 'sponsor',
                    'increase_power' => 1,
                    'increase_regard' => 2,
                    'notes' => 'Notes about the relation',
                    'faction' => false,
                    'id' => (string)Str::uuid(),
                ],
            ],
        ]);
        self::assertCount(1, $character->relations);
        $relation = $character->relations[0];
        self::assertSame(7, $relation->power);
        self::assertSame(7, $relation->regard);
        // Base relation fortune is 30. Sponsor level costs 10. Increasing
        // power costs 5 per level. Increasing regard is two per level.
        self::assertSame(30 - 10 - 5 - 4, $character->relation_fortune);

        // Relation fortune gets spent first, so this character should have all
        // of their starting fortune.
        self::assertSame(PartialCharacter::STARTING_FORTUNE, $character->fortune);
    }

    public function testSetRelations(): void
    {
        $character = new PartialCharacter();
        self::assertCount(0, $character->relations);

        $character->relations = [
            new Relation(
                name: 'Bob King',
                skills: [new Skill('influence')],
                archetypes: [new RelationArchetype('care', 'a category')],
                aspects: [new RelationAspect('adversarial')],
                power: 7,
                regard: 6,
                notes: 'notes',
                level: new RelationLevel('sponsor'),
                faction: false,
            ),
        ];

        self::assertCount(1, $character->relations);
    }

    public function testFortuneWithTooMuchRelation(): void
    {
        // Character has 4 sponsors at 10 fortune each.
        $character = new PartialCharacter([
            'relations' => [
                [
                    'name' => 'Test Relation',
                    'skill' => 'influence',
                    'archetype' => 'care',
                    'level' => 'sponsor',
                ],
                [
                    'name' => 'Test Relation',
                    'skill' => 'influence',
                    'archetype' => 'care',
                    'level' => 'sponsor',
                ],
                [
                    'name' => 'Test Relation',
                    'skill' => 'influence',
                    'archetype' => 'care',
                    'level' => 'sponsor',
                ],
                [
                    'name' => 'Test Relation',
                    'skill' => 'influence',
                    'archetype' => 'care',
                    'level' => 'sponsor',
                ],
            ],
        ]);
        self::assertSame(-10, $character->relation_fortune);
        self::assertSame(PartialCharacter::STARTING_FORTUNE - 10, $character->fortune);
    }
}
