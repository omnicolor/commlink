<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Models;

use Modules\Avatar\Enums\TechniqueClass;
use Modules\Avatar\Enums\TechniqueType;
use Modules\Avatar\Models\Technique;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class TechniqueTest extends TestCase
{
    public function testLoadUniversalTechnique(): void
    {
        $technique = Technique::findOrFail('attack-weakness');
        self::assertSame(TechniqueClass::AdvanceAndAttack, $technique->class);
        self::assertSame('attack-weakness', $technique->id);
        self::assertSame('Attack Weakness', (string)$technique);
        self::assertSame(280, $technique->page);
        self::assertFalse($technique->rare);
        self::assertSame('core', $technique->ruleset);
        self::assertNull($technique->specialization);
        self::assertSame(TechniqueType::Universal, $technique->type);
    }

    public function testLoadRareAndSpecializedTechnique(): void
    {
        $technique = Technique::findOrFail('detect-the-heavy-step');
        self::assertSame(TechniqueClass::DefendAndManeuver, $technique->class);
        self::assertSame('detect-the-heavy-step', $technique->id);
        self::assertSame('Detect the Heavy Step', (string)$technique);
        self::assertSame(283, $technique->page);
        self::assertTrue($technique->rare);
        self::assertSame('core', $technique->ruleset);
        self::assertSame('seismic sense', $technique->specialization);
        self::assertSame(TechniqueType::Earthbending, $technique->type);
    }
}
