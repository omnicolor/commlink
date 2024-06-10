<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Cyberpunkred;

use App\Models\Cyberpunkred\Character;
use App\Models\Cyberpunkred\Skill;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

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

    /**
     * Return attributes and what the short form should be.
     * @return array<int, array<int, string>>
     */
    public static function attributeProvider(): array
    {
        return [
            ['body', 'BOD'],
            ['cool', 'COOL'],
            ['dexterity', 'DEX'],
            ['empathy', 'EMP'],
            ['intelligence', 'INT'],
            ['reflexes', 'REF'],
            ['technique', 'TECH'],
            ['unknown', 'unknown'],
            ['willpower', 'WILL'],
        ];
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
