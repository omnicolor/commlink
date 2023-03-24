<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\CrewCompetence;
use Tests\TestCase;

/**
 * Tests for crew competence enum.
 * @group expanse
 * @group models
 * @small
 */
final class CrewCompetenceTest extends TestCase
{
    /**
     * Data provider for testing crew competence bonuses.
     * @return array<int, array<int, CrewCompetence|int>>
     */
    public static function competenceProvider(): array
    {
        return [
            [CrewCompetence::Incompetent, 0],
            [CrewCompetence::Poor, 1],
            [CrewCompetence::Average, 2],
            [CrewCompetence::Capable, 3],
            [CrewCompetence::Skilled, 4],
            [CrewCompetence::Elite, 5],
        ];
    }

    /**
     * @dataProvider competenceProvider
     * @param CrewCompetence $competence
     * @param int $bonus
     * @test
     */
    public function testBonus(CrewCompetence $competence, int $bonus): void
    {
        self::assertSame($bonus, $competence->bonus());
    }
}
