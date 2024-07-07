<?php

declare(strict_types=1);

namespace Modules\Subversion\Tests\Feature\Models;

use ErrorException;
use Illuminate\Support\Str;
use Modules\Subversion\Models\Relation;
use Modules\Subversion\Models\RelationArchetype;
use Modules\Subversion\Models\RelationAspect;
use Modules\Subversion\Models\RelationLevel;
use Modules\Subversion\Models\Skill;
use Tests\TestCase;

final class RelationTest extends TestCase
{
    public function testToString(): void
    {
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [],
            1,
            1,
            '',
            new RelationLevel('sponsor'),
        );
        self::assertSame('Bob King', (string)$relation);
    }

    public function testFromArray(): void
    {
        $relation = Relation::fromArray([
            'archetypes' => [
                ['id' => 'care'],
                ['id' => 'clout'],
            ],
            'aspects' => ['paternalistic'],
            'level' => 'big-shot',
            'name' => 'Phil Schept',
            'notes' => 'These are notes.',
            'increase_power' => 1,
            'increase_regard' => 1,
            'skills' => ['arts'],
            'faction' => false,
            'id' => (string)Str::uuid(),
        ]);

        self::assertSame('Care', $relation->archetypes[0]->name);
        self::assertSame('Paternalistic', $relation->aspects[0]->name);
        self::assertSame('Big shot', $relation->level->name);
        self::assertSame('Phil Schept', $relation->name);
        self::assertSame(7, $relation->power);
        self::assertSame(2, $relation->regard);
        self::assertSame('These are notes.', $relation->notes);
        self::assertSame('Arts', $relation->skills[0]->name);
        self::assertFalse($relation->faction);
    }

    public function testToArray(): void
    {
        $uuid = (string)Str::uuid();
        $relation = new Relation(
            'Test Relation',
            [new Skill('influence'), new Skill('sciences')],
            [new RelationArchetype('care', 'guns')],
            [new RelationAspect('multi-talented')],
            6,
            12,
            'Notez',
            new RelationLevel('friend'),
            false,
            $uuid,
        );

        self::assertSame(
            [
                'name' => 'Test Relation',
                'skills' => ['influence', 'sciences'],
                'archetypes' => [['id' => 'care', 'additional' => 'guns']],
                'aspects' => ['multi-talented'],
                'level' => 'friend',
                'increase_power' => 2,
                'increase_regard' => 2,
                'notes' => 'Notez',
                'faction' => false,
                'id' => $uuid,
            ],
            $relation->toArray(),
        );
    }

    public function testGetUnknownMethod(): void
    {
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [],
            1,
            1,
            '',
            new RelationLevel('sponsor'),
        );
        self::expectException(ErrorException::class);
        // @phpstan-ignore-next-line
        $relation->unknown;
    }

    public function testCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [],
            $level->power,
            $level->regard,
            '',
            $level,
        );

        self::assertSame(10, $relation->cost);
        $relation->power = $relation->power + 1;
        self::assertSame(15, $relation->cost);
        $relation->regard = $relation->regard + 1;
        self::assertSame(17, $relation->cost);
    }

    public function testAdversarialCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [new RelationAspect('adversarial')],
            $level->power,
            -10,
            '',
            $level,
        );

        self::assertSame(0, $relation->cost);
    }

    public function testDuesCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [new RelationAspect('dues')],
            $level->power,
            $level->regard,
            '',
            $level,
        );

        self::assertSame(5, $relation->cost);
    }

    public function testMultiTalentedCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [new Skill('influence')],
            [new RelationArchetype('care')],
            [new RelationAspect('multi-talented')],
            $level->power,
            $level->regard,
            '',
            $level,
        );

        self::assertSame(10, $relation->cost);
        $relation->skills = [new Skill('influence'), new Skill('sciences')];
        self::assertSame(15, $relation->cost);
        $relation->skills = [
            new Skill('arts'),
            new Skill('deception'),
            new Skill('influence'),
        ];
        self::assertSame(20, $relation->cost);
        $relation->archetypes = [
            new RelationArchetype('care'),
            new RelationArchetype('clout'),
        ];
        self::assertSame(25, $relation->cost);
    }

    public function testSupportiveCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [new RelationAspect('supportive')],
            $level->power,
            $level->regard,
            '',
            $level,
        );

        self::assertSame(25, $relation->cost);
    }

    public function testToxicCost(): void
    {
        $level = new RelationLevel('sponsor');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [new RelationAspect('toxic')],
            $level->power,
            $level->regard,
            '',
            $level,
        );
        self::assertSame(5, $relation->cost);

        $level = new RelationLevel('big-shot');
        $relation = new Relation(
            'Bob King',
            [],
            [],
            [new RelationAspect('toxic')],
            $level->power,
            $level->regard,
            '',
            $level,
        );
        self::assertSame(1, $relation->cost);
    }
}
