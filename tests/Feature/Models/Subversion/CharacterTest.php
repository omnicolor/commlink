<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Subversion;

use App\Models\Subversion\Character;
use App\Models\Subversion\Lineage;
use Tests\TestCase;

/**
 * @group subversion
 * @small
 */
final class CharacterTest extends TestCase
{
    public function testToStringNoName(): void
    {
        $character = new Character();
        self::assertSame('Unnamed character', (string)$character);
    }

    public function testToString(): void
    {
        $character = new Character(['name' => 'Derf']);
        self::assertSame('Derf', (string)$character);
    }

    public function testGritStarting(): void
    {
        $character = new Character(['will' => 5]);
        self::assertSame(11, $character->grit_starting);
        $character = new Character(['will' => 2]);
        self::assertSame(8, $character->grit_starting);
    }

    public function testLineage(): void
    {
        $character = new Character([
            'lineage' => 'dwarven',
            'lineage_option' => 'small',
        ]);

        self::assertSame('Dwarven', $character->lineage->name);
        // @phpstan-ignore-next-line
        self::assertSame('Small', $character->lineage->option->name);
    }

    public function testSetLineage(): void
    {
        $character = new Character();
        $character->lineage = new Lineage('dwarven', 'toxin-resistant');

        self::assertSame('Dwarven', $character->lineage->name);
        // @phpstan-ignore-next-line
        self::assertSame('Toxin resistant', $character->lineage->option->name);
    }
}
