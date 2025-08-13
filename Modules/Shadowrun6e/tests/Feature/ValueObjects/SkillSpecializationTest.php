<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Modules\Shadowrun6e\Enums\SpecializationLevel;
use Modules\Shadowrun6e\ValueObjects\SkillSpecialization;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class SkillSpecializationTest extends TestCase
{
    public function testToString(): void
    {
        $specialization = new SkillSpecialization('Shotguns');
        self::assertSame('Shotguns', (string)$specialization);

        $specialization = new SkillSpecialization(
            'Sniper Rifles',
            SpecializationLevel::Specialization,
        );
        self::assertSame('Sniper Rifles', (string)$specialization);

        $specialization = new SkillSpecialization(
            'Pistols',
            SpecializationLevel::Expertise,
        );
        self::assertSame('Pistols (E)', (string)$specialization);
    }
}
