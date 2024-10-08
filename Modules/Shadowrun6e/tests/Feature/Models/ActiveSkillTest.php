<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\ActiveSkill;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class ActiveSkillTest extends TestCase
{
    /**
     * Test trying to load an invalid skill.
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
     */
    public function testLoadSkill(): void
    {
        $skill = new ActiveSkill('astral', 4, 'Astral Combat');
        self::assertSame('Astral', (string)$skill);
        self::assertSame('astral', $skill->id);
        self::assertSame('Astral Combat', $skill->specialization);
        self::assertSame(93, $skill->page);
        self::assertFalse($skill->untrained);
        self::assertSame('intuition', $skill->attribute);
        self::assertSame('willpower', $skill->attribute_secondary);
        self::assertSame(4, $skill->level);
    }
}
