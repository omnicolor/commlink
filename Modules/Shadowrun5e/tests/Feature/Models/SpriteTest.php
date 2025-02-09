<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use BadMethodCallException;
use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\SkillArray;
use Modules\Shadowrun5e\Models\Sprite;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpriteTest extends TestCase
{
    /**
     * Test loading an invalid Sprite.
     */
    public function testSpriteNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Sprite ID "not-found" is invalid');
        new Sprite('not-found');
    }

    /**
     * Test loading a valid sprite without a level.
     */
    public function testSpriteNoLevel(): void
    {
        $sprite = new Sprite('courier');
        self::assertSame('Courier', $sprite->name);
        self::assertSame('core', $sprite->ruleset);
        self::assertSame(['cookie', 'hash'], $sprite->powers);
        self::assertNull($sprite->level);
    }

    /**
     * Test setting the level during instantiation works.
     */
    public function testSpriteWithLevel(): void
    {
        $sprite = new Sprite('courier', 6);
        self::assertSame(6, $sprite->level);
    }

    /**
     * Test that the __toString method returns the sprite's name.
     */
    public function testToString(): void
    {
        $sprite = new Sprite('courier');
        self::assertSame('Courier', (string)$sprite);
    }

    /**
     * Test trying to get an invalid attribute for a sprite.
     */
    public function testGetAttributeInvalid(): void
    {
        self::expectException(BadMethodCallException::class);
        self::expectExceptionMessage('Foo is not an attribute of sprites');
        $sprite = new Sprite('courier');
        // @phpstan-ignore method.notFound
        $sprite->getFoo();
    }

    /**
     * Test calculating a sprite's attributes from their level when no level is
     * set.
     */
    public function testGetAttributeNoLevel(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Level has not been set');
        $sprite = new Sprite('courier');
        $sprite->getFirewall();
    }

    /**
     * Test getting attributes after the level has been set.
     */
    public function testGetAttributeWithLevel(): void
    {
        $sprite = new Sprite('courier', 6);
        self::assertSame(6, $sprite->getAttack());
        self::assertSame(7, $sprite->getDataProcessing());
        self::assertSame(8, $sprite->getFirewall());
        self::assertSame(13, $sprite->getInitiative());
        self::assertSame(6, $sprite->getResonance());
        self::assertSame(9, $sprite->getSleaze());
    }

    /**
     * Test getting attributes after setting the level post-instantiation.
     */
    public function testGetAttributeWithLateSetLevel(): void
    {
        $sprite = new Sprite('courier');
        $sprite->setLevel(3);
        self::assertSame(3, $sprite->getAttack());
        self::assertSame(4, $sprite->getDataProcessing());
        self::assertSame(5, $sprite->getFirewall());
        self::assertSame(7, $sprite->getInitiative());
        self::assertSame(3, $sprite->getResonance());
        self::assertSame(6, $sprite->getSleaze());
    }

    /**
     * Test trying to get the skills list before the level is set.
     */
    public function testSkillsNoLevel(): void
    {
        $expected = ['computer', 'hacking'];
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Level is not set');
        $sprite = new Sprite('courier');
        self::assertEqualsCanonicalizing($expected, $sprite->skills);
        $sprite->getSkills();
    }

    /**
     * Test that a Sprite gets its skills initialized if given a level.
     */
    public function testSkills(): void
    {
        $expected = new SkillArray();
        $expected[] = new ActiveSkill('computer', 3);
        $expected[] = new ActiveSkill('hacking', 3);

        $sprite = new Sprite('courier', 3);
        self::assertEqualsCanonicalizing($expected, $sprite->getSkills());
    }

    /**
     * Test getting a sprite's powers.
     */
    public function testGetPowers(): void
    {
        $sprite = new Sprite('courier');
        $powers = $sprite->getPowers();
        self::assertCount(2, $powers);
        self::assertSame('Cookie', $powers[0]->name);
    }
}
