<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Race;
use Modules\Shadowrun6e\ValueObjects\BaselineAttribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class RaceTest extends TestCase
{
    public function testToString(): void
    {
        $race = Race::findOrFail('human');
        self::assertSame('Human', (string)$race);
    }

    public function testAgility(): void
    {
        $race = Race::findOrFail('human');
        self::assertEquals(
            new BaselineAttribute(1, 6, 'agility'),
            $race->agility,
        );
    }

    public function testBody(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 7, 'body'),
            $race->body,
        );
    }

    public function testCharisma(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 6, 'charisma'),
            $race->charisma,
        );
    }

    public function testEdge(): void
    {
        $race = Race::findOrFail('human');
        self::assertEquals(
            new BaselineAttribute(1, 7, 'edge'),
            $race->edge,
        );
    }

    public function testIntuition(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 6, 'intuition'),
            $race->intuition,
        );
    }

    public function testLogic(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 6, 'logic'),
            $race->logic,
        );
    }

    public function testReaction(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 5, 'reaction'),
            $race->reaction,
        );
    }

    public function testStrength(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 8, 'strength'),
            $race->strength,
        );
    }

    public function testWillpower(): void
    {
        $race = Race::findOrFail('dwarf');
        self::assertEquals(
            new BaselineAttribute(1, 7, 'willpower'),
            $race->willpower,
        );
    }
}
