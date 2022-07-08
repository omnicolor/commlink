<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun6e;

use App\Models\Shadowrun6e\ActiveSkill;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Shadowrun 6E active skills.
 * @group models
 * @group shadowrun
 * @group shadowrun6e
 * @small
 */
final class ActiveSkillTest extends TestCase
{
    /**
     * Test trying to load an invalid skill.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Shadowrun 6E skill ID "not-found" is invalid'
        );
        new ActiveSkill('not-found');
    }

    /**
     * Test loading a valid skill.
     * @test
     */
    public function testLoadSkill(): void
    {
        $skill = new ActiveSkill('astral', 4, 'Astral Combat');
        self::assertSame('Astral', (string)$skill);
        self::assertSame('astral', $skill->id);
        self::assertSame('Astral Combat', $skill->specialization);
        self::assertNotNull($skill->description);
        self::assertSame(93, $skill->page);
        self::assertFalse($skill->untrained);
        self::assertSame('intuition', $skill->attribute);
        self::assertSame('willpower', $skill->attribute_secondary);
        self::assertSame(4, $skill->level);
    }
}
