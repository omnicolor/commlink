<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Enums\SpecializationLevel;
use Modules\Shadowrun6e\Models\ActiveSkill;
use Modules\Shadowrun6e\ValueObjects\SkillSpecialization;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RangeException;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class ActiveSkillTest extends TestCase
{
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        ActiveSkill::findOrFail('not-found');
    }

    public function testSkillWithSecondaryAttributes(): void
    {
        $skill = ActiveSkill::findOrFail('astral');
        self::assertSame('Astral', (string)$skill);
        self::assertSame('astral', $skill->id);
        self::assertSame(0, $skill->level);
        self::assertSame(
            [
                'Astral Combat',
                'Astral Signatures',
                'Emotional States',
                'Spirit Types',
            ],
            $skill->example_specializations,
        );
        self::assertSame([], $skill->specializations);
        self::assertSame(93, $skill->page);
        self::assertFalse($skill->untrained);
        self::assertSame('intuition', $skill->attribute);
        self::assertSame(['willpower'], $skill->attributes_secondary);
        self::assertSame(0, $skill->level);
    }

    public function testSkillWithoutSecondaryAttributes(): void
    {
        $skill = ActiveSkill::findOrFail('close-combat');
        self::assertNull($skill->attributes_secondary);
    }

    public function testMakeSkill(): void
    {
        // @phpstan-ignore larastan.noModelMake
        $skill = ActiveSkill::make([
            'id' => 'astral',
            'level' => 4,
        ]);
        self::assertEmpty($skill->specializations);
    }

    public function testMakeSkillWithSpecializations(): void
    {
        // @phpstan-ignore larastan.noModelMake
        $skill = ActiveSkill::make([
            'id' => 'astral',
            'level' => 4,
            'specializations' => [
                ['name' => 'Astral Combat'],
                ['name' => 'Astral Signatures', 'level' => 1],
            ],
        ]);
        self::assertSame(4, $skill->level);
        self::assertEquals(
            [
                new SkillSpecialization(
                    'Astral Combat',
                    SpecializationLevel::Specialization,
                ),
                new SkillSpecialization(
                    'Astral Signatures',
                    SpecializationLevel::Expertise,
                ),
            ],
            $skill->specializations,
        );
    }

    public function testSkillSetLevel(): void
    {
        $skill = ActiveSkill::findOrFail('close-combat');
        $skill->level = 4;
        self::assertSame(4, $skill->level);
    }

    public function testSkillLevelTooLow(): void
    {
        $skill = ActiveSkill::findOrFail('close-combat');
        self::expectException(RangeException::class);
        $skill->level = -1;
    }

    public function testSkillLevelTooHigh(): void
    {
        $skill = ActiveSkill::findOrFail('close-combat');
        self::expectException(RangeException::class);
        $skill->level = 10;
    }

    public function testSkillSetSpecializations(): void
    {
        $skill = ActiveSkill::findOrFail('astral');
        self::assertCount(0, $skill->specializations);
        $skill->specializations = [
            ['name' => 'Astral Combat'],
            new SkillSpecialization(
                'Astral Signatures',
                SpecializationLevel::Expertise,
            ),
        ];
        self::assertCount(2, $skill->specializations);
        self::assertSame('Astral Combat', (string)$skill->specializations[0]);
        self::assertSame('Astral Signatures (E)', (string)$skill->specializations[1]);
    }
}
