<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\Enums;

use Modules\Avatar\Enums\TechniqueLevel;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class TechniqueLevelTest extends TestCase
{
    public function testIsLearned(): void
    {
        self::assertTrue(TechniqueLevel::Learned->isLearned());
        self::assertTrue(TechniqueLevel::Practiced->isLearned());
        self::assertTrue(TechniqueLevel::Mastered->isLearned());
    }

    public function testIsPracticed(): void
    {
        self::assertFalse(TechniqueLevel::Learned->isPracticed());
        self::assertTrue(TechniqueLevel::Practiced->isPracticed());
        self::assertTrue(TechniqueLevel::Mastered->isPracticed());
    }

    public function testIsMastered(): void
    {
        self::assertFalse(TechniqueLevel::Learned->isMastered());
        self::assertFalse(TechniqueLevel::Practiced->isMastered());
        self::assertTrue(TechniqueLevel::Mastered->isMastered());
    }
}
