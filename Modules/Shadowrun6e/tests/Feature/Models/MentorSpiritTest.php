<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\MentorSpirit;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class MentorSpiritTest extends TestCase
{
    public function testToString(): void
    {
        $mentor = MentorSpirit::findOrFail('bear');
        self::assertSame('Bear', (string)$mentor);
    }

    public function testAdvantages(): void
    {
        $mentor = MentorSpirit::findOrFail('bear');

        $advantages = $mentor->advantages;
        self::assertArrayHasKey('all', $advantages);
        self::assertArrayHasKey('magician', $advantages);
        self::assertArrayHasKey('adept', $advantages);
    }
}
