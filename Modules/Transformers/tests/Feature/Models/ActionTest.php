<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\Action;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class ActionTest extends TestCase
{
    /**
     * @return array<int, array<int, string|Action>>
     */
    public static function firstWordProvider(): array
    {
        return [
            [Action::Accuracy, 'Target'],
            [Action::Acrobatics, 'Jumping'],
            [Action::Assault, 'Fire'],
            [Action::Communication, 'Free'],
            [Action::Construction, 'Creation'],
            [Action::Dash, 'Roll'],
            [Action::Data, 'Know'],
            [Action::Defence, 'Roll'],
            [Action::Demolition, 'Free'],
            [Action::Espionage, 'Stealth'],
            [Action::Intercept, 'Gain'],
            [Action::Invention, 'Draw'],
            [Action::Materials, 'Interact'],
            [Action::MeleeAttack, 'Attack'],
            [Action::Repair, 'Restore'],
            [Action::Sabotage, 'Affect'],
            [Action::Strategy, 'Project'],
            [Action::Support, 'Roll'],
            [Action::Surveillance, 'Visual'],
            [Action::Transform, 'Once'],
            [Action::Trooper, 'Summon'],
        ];
    }

    #[DataProvider('firstWordProvider')]
    public function testDescription(Action $action, string $firstWord): void
    {
        // @phpstan-ignore-next-line
        self::assertStringStartsWith($firstWord, $action->description());
    }

    /**
     * @return array<int, array<int, int|Action>>
     */
    public static function programmingCountProvider(): array
    {
        return [
            [Action::Accuracy, 1],
            [Action::Acrobatics, 1],
            [Action::Assault, 4],
            [Action::Communication, 1],
            [Action::Construction, 1],
            [Action::Dash, 2],
            [Action::Data, 1],
            [Action::Defence, 2],
            [Action::Demolition, 1],
            [Action::Espionage, 1],
            [Action::Intercept, 1],
            [Action::Invention, 1],
            [Action::Materials, 1],
            [Action::MeleeAttack, 4],
            [Action::Repair, 1],
            [Action::Sabotage, 1],
            [Action::Strategy, 1],
            [Action::Support, 1],
            [Action::Surveillance, 1],
            [Action::Transform, 4],
            [Action::Trooper, 1],
        ];
    }

    #[DataProvider('programmingCountProvider')]
    public function testProgrammingCount(Action $action, int $count): void
    {
        self::assertCount($count, $action->programming());
    }

    /**
     * @return array<int, array<int, string|Action>>
     */
    public static function actionStatisticProvider(): array
    {
        return [
            [Action::Accuracy, 'skill'],
            [Action::Acrobatics, 'skill'],
            [Action::Assault, 'firepower'],
            [Action::Communication, 'skill'],
            [Action::Construction, 'endurance'],
            [Action::Dash, 'speed'],
            [Action::Data, 'speed'],
            [Action::Defence, 'endurance'],
            [Action::Demolition, 'intelligence'],
            [Action::Espionage, 'courage'],
            [Action::Intercept, 'courage'],
            [Action::Invention, 'intelligence'],
            [Action::Materials, 'courage'],
            [Action::MeleeAttack, 'strength'],
            [Action::Repair, 'skill'],
            [Action::Sabotage, 'endurance'],
            [Action::Strategy, 'intelligence'],
            [Action::Support, 'speed'],
            [Action::Surveillance, 'intelligence'],
            [Action::Transform, 'rank'],
            [Action::Trooper, 'courage'],
        ];
    }

    #[DataProvider('actionStatisticProvider')]
    public function testStatistic(Action $action, string $stat): void
    {
        self::assertSame($stat, $action->statistic());
    }
}
