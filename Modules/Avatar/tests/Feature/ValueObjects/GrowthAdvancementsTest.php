<?php

declare(strict_types=1);

namespace Modules\Avatar\Tests\Feature\ValueObjects;

use Modules\Avatar\ValueObjects\GrowthAdvancements;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RangeException;
use Tests\TestCase;

#[Group('avatar')]
#[Small]
final class GrowthAdvancementsTest extends TestCase
{
    public function testDefault(): void
    {
        $growth = new GrowthAdvancements([]);
        self::assertSame(0, $growth->new_move_from_my_playbook);
        self::assertSame(0, $growth->new_move_from_another_playbook);
        self::assertSame(0, $growth->shift_your_center);
        self::assertSame(0, $growth->unlock_your_moment_of_balance);
    }

    public function testWithValues(): void
    {
        $growth = new GrowthAdvancements([
            'new_move_from_my_playbook' => 1,
            'new_move_from_another_playbook' => 1,
            'shift_your_center' => 1,
            'unlock_your_moment_of_balance' => 1,
        ]);
        self::assertSame(1, $growth->new_move_from_my_playbook);
        self::assertSame(1, $growth->new_move_from_another_playbook);
        self::assertSame(1, $growth->shift_your_center);
        self::assertSame(1, $growth->unlock_your_moment_of_balance);

        $growth = new GrowthAdvancements([
            'new_move_from_my_playbook' => 2,
            'new_move_from_another_playbook' => 2,
            'shift_your_center' => 2,
            'unlock_your_moment_of_balance' => 2,
        ]);
        self::assertSame(2, $growth->new_move_from_my_playbook);
        self::assertSame(2, $growth->new_move_from_another_playbook);
        self::assertSame(2, $growth->shift_your_center);
        self::assertSame(2, $growth->unlock_your_moment_of_balance);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function advancementProvider(): array
    {
        return [
            ['new_move_from_my_playbook'],
            ['new_move_from_another_playbook'],
            ['shift_your_center'],
            ['unlock_your_moment_of_balance'],
        ];
    }

    #[DataProvider('advancementProvider')]
    public function testTooLow(string $advancement): void
    {
        self::expectException(RangeException::class);
        self::expectExceptionMessage(
            'Growth advancements can not be less than zero',
        );
        // @phpstan-ignore argument.type
        new GrowthAdvancements([$advancement => -1]);
    }

    #[DataProvider('advancementProvider')]
    public function testTooHigh(string $advancement): void
    {
        self::expectException(RangeException::class);
        self::expectExceptionMessage(
            'Growth advancements can not be greater than two',
        );
        // @phpstan-ignore argument.type
        new GrowthAdvancements([$advancement => 3]);
    }
}
