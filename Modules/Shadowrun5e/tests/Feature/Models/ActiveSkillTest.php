<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ActiveSkill;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use RuntimeException;
use Tests\TestCase;

#[CoversClass(ActiveSkill::class)]
#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ActiveSkillTest extends TestCase
{
    #[Test]
    #[TestDox('Loading an invalid skill throws an exception')]
    public function testLoadingInvalidSkill(): void
    {
        ActiveSkill::$skills = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Skill ID "not-found-id" is invalid');
        new ActiveSkill('not-found-id', 0);
    }

    #[Test]
    #[TestDox('Skills load with correct properties')]
    public function testLoadSkillSetsId(): void
    {
        $skill = new ActiveSkill('automatics', 4);
        self::assertSame('automatics', $skill->id);
        self::assertSame('agility', $skill->attribute);
        self::assertTrue($skill->default);
        self::assertSame('firearms', $skill->group);
        self::assertSame(4, $skill->level);
        self::assertSame('Automatics', $skill->name);
    }

    #[Test]
    #[TestDox('Skills can be cast to a string')]
    public function testLoadSkillToString(): void
    {
        $skill = new ActiveSkill('automatics', 4);
        self::assertSame('Automatics', (string)$skill);
    }

    #[Test]
    #[TestDox('Skills that do not have a group return null for their group')]
    public function testLoadGrouplessSkill(): void
    {
        $skill = new ActiveSkill('astral-combat', 3);
        self::assertNull($skill->group);
    }

    #[Test]
    #[TestDox('Skills that can not be defaulted return false for default property')]
    public function testLoadNotDefaultableSkill(): void
    {
        $skill = new ActiveSkill('astral-combat', 3);
        self::assertFalse($skill->default);
    }

    #[Test]
    #[TestDox('ActiveSkill::findIdByName() throws an exception if a skill name is not found')]
    public function testFindIdByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Active skill "Foo" not found');
        ActiveSkill::$skills = null;
        ActiveSkill::findIdByName('Foo');
    }

    #[Test]
    #[TestDox('ActiveSkill::findIdByName() can find a skill\'s ID given its name')]
    public function testFindIdByName(): void
    {
        self::assertSame(
            'automatics',
            ActiveSkill::findIdByName('Automatics')
        );
        self::assertSame(
            'astral-combat',
            ActiveSkill::findIdByName('Astral Combat')
        );
    }
}
