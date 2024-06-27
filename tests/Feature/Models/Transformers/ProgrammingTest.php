<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Action;
use App\Models\Transformers\Programming;
use Tests\TestCase;

/**
 * @group models
 * @group transformers
 * @small
 */
final class ProgrammingTest extends TestCase
{
    /**
     * @return array<int, array<int, Action|string>>
     */
    public static function programmingProvider(): array
    {
        return [
            ['engineer', Action::Materials],
            ['gunner', Action::Trooper],
            ['scout', Action::Espionage],
            ['warrior', Action::Intercept],
        ];
    }

    /**
     * @dataProvider programmingProvider
     */
    public function testActionForStrength(string $programming): void
    {
        $programming = Programming::from($programming);
        self::assertSame(
            Action::MeleeAttack,
            $programming->actions()['strength']
        );
    }

    /**
     * @dataProvider programmingProvider
     */
    public function testActionForCourage(string $programming, Action $action): void
    {
        $programming = Programming::from($programming);
        self::assertSame($action, $programming->actions()['courage']);
    }

    /**
     * @return array<int, array<int, string|Programming>>
     */
    public static function descriptionProvider(): array
    {
        return [
            [Programming::Engineer, '18%'],
            [Programming::Gunner, '19%'],
            [Programming::Scout, '24%'],
            [Programming::Warrior, '43%'],
        ];
    }

    /**
     * @dataProvider descriptionProvider
     */
    public function testDescription(Programming $programming, string $percent): void
    {
        self::assertStringContainsString($percent, $programming->description());
    }

    public function testAll(): void
    {
        self::assertCount(4, Programming::all());
    }
}
