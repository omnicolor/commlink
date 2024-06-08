<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\CrewCompetence;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
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
     */
    public function testBonus(CrewCompetence $competence, int $bonus): void
    {
        self::assertSame($bonus, $competence->bonus());
    }
}
