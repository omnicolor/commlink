<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Tests\Feature\Models;

use Iterator;
use Modules\Cyberpunkred\Models\Character;
use Modules\Cyberpunkred\Models\Skill;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

use function random_int;

#[Group('cyberpunkred')]
#[Small]
final class SkillTest extends TestCase
{
    /**
     * Test trying to load an invalid skill throws an exception.
     */
    public function testLoadingInvalidSkill(): void
    {
        Skill::$skills = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Skill ID "not-found-id" is invalid');
        new Skill('not-found-id', 0);
    }

    /**
     * Test that loading a skill sets all of the fields.
     */
    public function testLoadingSetsFields(): void
    {
        $skill = new Skill('business');
        self::assertSame('intelligence', $skill->attribute);
        self::assertSame('Education', $skill->category);
        self::assertNotEmpty($skill->description);
        self::assertNotEmpty($skill->examples);
        self::assertSame('business', $skill->id);
        self::assertSame(0, $skill->level);
        self::assertSame('Business', $skill->name);
        self::assertSame(133, $skill->page);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $skill = new Skill('business');
        self::assertSame('Business', (string)$skill);
    }

    /**
     * Test getBase().
     */
    public function testGetBase(): void
    {
        $intelligence = random_int(1, 8);
        $level = random_int(1, 8);
        $character = new Character(['intelligence' => $intelligence]);
        $skill = new Skill('business', $level);
        self::assertSame($intelligence + $level, $skill->getBase($character));
    }

    public function testGetBaseInvalidAttribte(): void
    {
        $character = new Character();
        $skill = new Skill('business', 1);
        $skill->attribute = 'invalid';
        self::expectException(RuntimeException::class);
        $skill->getBase($character);
    }

    /**
     * Return attributes and what the short form should be.
     * @return Iterator<int, array<int, string>>
     */
    public static function attributeProvider(): Iterator
    {
        yield ['body', 'BOD'];
        yield ['cool', 'COOL'];
        yield ['dexterity', 'DEX'];
        yield ['empathy', 'EMP'];
        yield ['intelligence', 'INT'];
        yield ['reflexes', 'REF'];
        yield ['technique', 'TECH'];
        yield ['unknown', 'unknown'];
        yield ['willpower', 'WILL'];
    }

    /**
     * Test getting a skill's shortened attribute.
     */
    #[DataProvider('attributeProvider')]
    public function testGetShort(string $attribute, string $expected): void
    {
        $skill = new Skill('business');
        $skill->attribute = $attribute;
        self::assertSame($expected, $skill->getShortAttribute());
    }
}
