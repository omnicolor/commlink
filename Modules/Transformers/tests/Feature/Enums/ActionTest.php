<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Enums;

use Iterator;
use Modules\Transformers\Enums\Action;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class ActionTest extends TestCase
{
    /**
     * @return Iterator<int, array{Action, non-empty-string}>
     */
    public static function firstWordProvider(): Iterator
    {
        yield [Action::Accuracy, 'Target'];
        yield [Action::Acrobatics, 'Jumping'];
        yield [Action::Assault, 'Fire'];
        yield [Action::Communication, 'Free'];
        yield [Action::Construction, 'Creation'];
        yield [Action::Dash, 'Roll'];
        yield [Action::Data, 'Know'];
        yield [Action::Defence, 'Roll'];
        yield [Action::Demolition, 'Free'];
        yield [Action::Espionage, 'Stealth'];
        yield [Action::Intercept, 'Gain'];
        yield [Action::Invention, 'Draw'];
        yield [Action::Materials, 'Interact'];
        yield [Action::MeleeAttack, 'Attack'];
        yield [Action::Repair, 'Restore'];
        yield [Action::Sabotage, 'Affect'];
        yield [Action::Strategy, 'Project'];
        yield [Action::Support, 'Roll'];
        yield [Action::Surveillance, 'Visual'];
        yield [Action::Transform, 'Once'];
        yield [Action::Trooper, 'Summon'];
    }

    /**
     * @param non-empty-string $firstWord
     */
    #[DataProvider('firstWordProvider')]
    public function testDescription(Action $action, string $firstWord): void
    {
        self::assertStringStartsWith($firstWord, $action->description());
    }

    /**
     * @return Iterator<int, array<int, (int | Action)>>
     */
    public static function programmingCountProvider(): Iterator
    {
        yield [Action::Accuracy, 1];
        yield [Action::Acrobatics, 1];
        yield [Action::Assault, 4];
        yield [Action::Communication, 1];
        yield [Action::Construction, 1];
        yield [Action::Dash, 2];
        yield [Action::Data, 1];
        yield [Action::Defence, 2];
        yield [Action::Demolition, 1];
        yield [Action::Espionage, 1];
        yield [Action::Intercept, 1];
        yield [Action::Invention, 1];
        yield [Action::Materials, 1];
        yield [Action::MeleeAttack, 4];
        yield [Action::Repair, 1];
        yield [Action::Sabotage, 1];
        yield [Action::Strategy, 1];
        yield [Action::Support, 1];
        yield [Action::Surveillance, 1];
        yield [Action::Transform, 4];
        yield [Action::Trooper, 1];
    }

    #[DataProvider('programmingCountProvider')]
    public function testProgrammingCount(Action $action, int $count): void
    {
        self::assertCount($count, $action->programming());
    }

    /**
     * @return Iterator<int, array<int, (Action | string)>>
     */
    public static function actionStatisticProvider(): Iterator
    {
        yield [Action::Accuracy, 'skill'];
        yield [Action::Acrobatics, 'skill'];
        yield [Action::Assault, 'firepower'];
        yield [Action::Communication, 'skill'];
        yield [Action::Construction, 'endurance'];
        yield [Action::Dash, 'speed'];
        yield [Action::Data, 'speed'];
        yield [Action::Defence, 'endurance'];
        yield [Action::Demolition, 'intelligence'];
        yield [Action::Espionage, 'courage'];
        yield [Action::Intercept, 'courage'];
        yield [Action::Invention, 'intelligence'];
        yield [Action::Materials, 'courage'];
        yield [Action::MeleeAttack, 'strength'];
        yield [Action::Repair, 'skill'];
        yield [Action::Sabotage, 'endurance'];
        yield [Action::Strategy, 'intelligence'];
        yield [Action::Support, 'speed'];
        yield [Action::Surveillance, 'intelligence'];
        yield [Action::Transform, 'rank'];
        yield [Action::Trooper, 'courage'];
    }

    #[DataProvider('actionStatisticProvider')]
    public function testStatistic(Action $action, string $stat): void
    {
        self::assertSame($stat, $action->statistic());
    }
}
