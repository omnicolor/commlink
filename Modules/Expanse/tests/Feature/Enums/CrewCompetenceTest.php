<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Enums;

use Iterator;
use Modules\Expanse\Enums\CrewCompetence;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class CrewCompetenceTest extends TestCase
{
    /**
     * Data provider for testing crew competence bonuses.
     * @return Iterator<int, array<int, (int | CrewCompetence)>>
     */
    public static function competenceProvider(): Iterator
    {
        yield [CrewCompetence::Incompetent, 0];
        yield [CrewCompetence::Poor, 1];
        yield [CrewCompetence::Average, 2];
        yield [CrewCompetence::Capable, 3];
        yield [CrewCompetence::Skilled, 4];
        yield [CrewCompetence::Elite, 5];
    }

    #[DataProvider('competenceProvider')]
    public function testBonus(CrewCompetence $competence, int $bonus): void
    {
        self::assertSame($bonus, $competence->bonus());
    }
}
