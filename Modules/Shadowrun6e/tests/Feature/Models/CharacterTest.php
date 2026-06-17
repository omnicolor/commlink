<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Illuminate\Foundation\Testing\WithFaker;
use Modules\Shadowrun6e\Models\Character;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class CharacterTest extends TestCase
{
    use WithFaker;

    public function testToStringNoHandleOrName(): void
    {
        $character = new Character();
        self::assertSame('Unnamed Character', (string)$character);
    }

    public function testToStringNoHandle(): void
    {
        $character = new Character(['name' => 'Phil']);
        self::assertSame('Phil', (string)$character);
    }

    public function testToString(): void
    {
        $character = new Character(['handle' => 'The Smiling Bandit']);
        self::assertSame('The Smiling Bandit', (string)$character);
    }

    public function testGetQualitiesNone(): void
    {
        $character = new Character();
        self::assertEmpty($character->qualities);
    }

    public function testGetQualitiesInvalid(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'not-found'],
            ],
        ]);
        self::assertEmpty($character->qualities);
    }

    public function testGetQualities(): void
    {
        $character = new Character([
            'qualities' => [
                ['id' => 'ambidextrous'],
                ['id' => 'focused-concentration-1'],
            ],
        ]);
        self::assertCount(2, $character->qualities);
    }

    public function testInitiative(): void
    {
        $intuition = $this->faker->randomDigit();
        $reaction = $this->faker->randomDigit();
        $character = new Character([
            'intuition' => $intuition,
            'reaction' => $reaction,
        ]);
        self::assertSame($intuition + $reaction, $character->initiative_base);
        self::assertSame(1, $character->initiative_dice);
    }

    public function testAgility(): void
    {
        $character = new Character(['agility' => 6]);
        self::assertSame(6, $character->agility->value);
    }

    public function testBody(): void
    {
        $character = new Character(['body' => 6]);
        self::assertSame(6, $character->body->value);
        self::assertSame(6, $character->body->base_value);
    }

    public function testCharisma(): void
    {
        $character = new Character(['charisma' => 5]);
        self::assertSame(5, $character->charisma->value);
    }

    public function testDrain(): void
    {
        $character = new Character();
        self::assertNull($character->drain_dice);

        $character = new Character([
            'logic' => 5,
            'tradition' => 'hermetic',
            'willpower' => 3,
        ]);
        self::assertSame(8, $character->drain_dice);
    }

    public function testEdge(): void
    {
        $character = new Character(['edge' => 6]);
        self::assertSame(6, $character->edge->value);
        self::assertSame(6, $character->edge_current->value);
        $character = new Character(['edge' => 6, 'edge_current' => 5]);
        self::assertSame(6, $character->edge->value);
        self::assertSame(5, $character->edge_current->value);
    }

    public function testIntuition(): void
    {
        $character = new Character(['intuition' => 6]);
        self::assertSame(6, $character->intuition->value);
    }

    public function testLogic(): void
    {
        $character = new Character(['logic' => 4]);
        self::assertSame(4, $character->logic->value);
    }

    public function testMagic(): void
    {
        $character = new Character(['magic' => 4]);
        self::assertSame(4, $character->magic?->value);
        $character = new Character();
        self::assertNull($character->magic);
    }

    public function testReaction(): void
    {
        $character = new Character(['reaction' => 4]);
        self::assertSame(4, $character->reaction->value);
    }

    public function testResonance(): void
    {
        $character = new Character();
        self::assertNull($character->resonance);
        $character = new Character(['resonance' => 5]);
        self::assertSame(5, $character->resonance?->value);
    }

    public function testStrength(): void
    {
        $character = new Character(['strength' => 4]);
        self::assertSame(4, $character->strength->value);
    }

    public function testWillpower(): void
    {
        $character = new Character(['willpower' => 3]);
        self::assertSame(3, $character->willpower->value);
    }

    public function testComposure(): void
    {
        $character = new Character(['charisma' => 5, 'willpower' => 6]);
        self::assertSame(11, $character->composure);
        $character = new Character(['charisma' => 2, 'willpower' => 2]);
        self::assertSame(4, $character->composure);
    }

    public function testJudgeIntentions(): void
    {
        $character = new Character(['intuition' => 3, 'willpower' => 4]);
        self::assertSame(7, $character->judge_intentions);
    }

    public function testLift(): void
    {
        $character = new Character(['body' => 4, 'willpower' => 1]);
        self::assertSame(5, $character->lift);
    }

    public function testMemory(): void
    {
        $character = new Character(['intuition' => 6, 'logic' => 6]);
        self::assertSame(12, $character->memory);
    }

    public function testSurpriseDice(): void
    {
        $character = new Character(['intuition' => 3, 'reaction' => 4]);
        self::assertSame(7, $character->surprise_dice);
    }

    public function testGetSkills(): void
    {
        $character = new Character();
        self::assertCount(0, $character->active_skills);
        $character = new Character([
            'active_skills' => [
                [
                    'id' => 'con',
                    'level' => 4,
                    'specializations' => [
                        [
                            'name' => 'Acting',
                        ],
                        [
                            'name' => 'Disguise',
                            'level' => 1,
                        ],
                    ],
                ],
            ],
        ]);
        self::assertCount(1, $character->active_skills);
        $skill = $character->active_skills[0];
        self::assertSame('con', $skill->id);
        self::assertSame(4, $skill->level);
        self::assertSame('Acting', $skill->specializations[0]->name);
        self::assertSame('Disguise (E)', (string)$skill->specializations[1]);
    }

    public function testAwakenedBooleans(): void
    {
        $character = new Character();
        self::assertFalse($character->isAwakened());
        self::assertFalse($character->isMagical());
        self::assertFalse($character->isTechnomancer());

        $character = new Character(['resonance' => 1]);
        self::assertTrue($character->isAwakened());
        self::assertFalse($character->isMagical());
        self::assertTrue($character->isTechnomancer());

        $character = new Character(['magic' => 1]);
        self::assertTrue($character->isAwakened());
        self::assertTrue($character->isMagical());
        self::assertFalse($character->isTechnomancer());
    }

    public function testTradition(): void
    {
        $character = new Character();
        self::assertNull($character->tradition);

        $character = new Character(['tradition' => 'hermetic']);
        self::assertSame('Hermetic', $character->tradition?->name);
    }
}
