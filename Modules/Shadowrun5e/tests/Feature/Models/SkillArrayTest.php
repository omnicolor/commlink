<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\ActiveSkill;
use Modules\Shadowrun5e\Models\Skill;
use Modules\Shadowrun5e\Models\SkillArray;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SkillArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var SkillArray<Skill>
     */
    protected SkillArray $skills;

    /**
     * Set up a clean subject.
     */
    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->skills = new SkillArray();
    }

    /**
     * Test an empty SkillArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->skills);
    }

    /**
     * Test adding a skill to the array.
     */
    public function testAdd(): void
    {
        $this->skills[] = new ActiveSkill('automatics', 3);
        self::assertNotEmpty($this->skills);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore offsetAssign.valueType
        $this->skills[] = new stdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore argument.type
            $this->skills->offsetSet(skill: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->skills);
    }
}
