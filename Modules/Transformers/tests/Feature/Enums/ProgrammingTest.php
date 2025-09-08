<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Enums;

use Iterator;
use Modules\Transformers\Enums\Action;
use Modules\Transformers\Enums\Programming;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
final class ProgrammingTest extends TestCase
{
    /**
     * @return Iterator<int, array<int, (Action | string)>>
     */
    public static function programmingProvider(): Iterator
    {
        yield ['engineer', Action::Materials];
        yield ['gunner', Action::Trooper];
        yield ['scout', Action::Espionage];
        yield ['warrior', Action::Intercept];
    }

    #[DataProvider('programmingProvider')]
    public function testActionForStrength(string $programming, Action $unused): void
    {
        $programming = Programming::from($programming);
        self::assertSame(
            Action::MeleeAttack,
            $programming->actions()['strength']
        );
    }

    #[DataProvider('programmingProvider')]
    public function testActionForCourage(string $programming, Action $action): void
    {
        $programming = Programming::from($programming);
        self::assertSame($action, $programming->actions()['courage']);
    }

    /**
     * @return Iterator<int, array<int, (Programming | string)>>
     */
    public static function descriptionProvider(): Iterator
    {
        yield [Programming::Engineer, '18%'];
        yield [Programming::Gunner, '19%'];
        yield [Programming::Scout, '24%'];
        yield [Programming::Warrior, '43%'];
    }

    #[DataProvider('descriptionProvider')]
    public function testDescription(Programming $programming, string $percent): void
    {
        self::assertStringContainsString($percent, $programming->description());
    }

    public function testAll(): void
    {
        self::assertCount(4, Programming::all());
    }
}
