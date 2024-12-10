<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Modules\Root\Models\Move;
use Modules\Root\Models\Nature;
use Modules\Root\Models\Playbook;
use Modules\Root\ValueObjects\Attribute;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('root')]
#[Small]
final class PlaybookTest extends TestCase
{
    public function testFindNotFound(): void
    {
        self::expectException(ModelNotFoundException::class);
        Playbook::findOrFail('not-found');
    }

    public function testLoad(): void
    {
        $playbook = Playbook::findOrFail('arbiter');
        self::assertSame('The Arbiter', (string)$playbook);
        self::assertEquals(new Attribute(1), $playbook->charm);
        self::assertEquals(new Attribute(0), $playbook->cunning);
        self::assertEquals(new Attribute(0), $playbook->finesse);
        self::assertEquals(new Attribute(-1), $playbook->luck);
        self::assertEquals(new Attribute(2), $playbook->might);
        self::assertSame(
            'You are the Arbiter. A powerful, obstinate vagabond, serving as '
                . 'somewhere between a mercenary and a protector, perhaps '
                . 'taking sides too easily in the greater conflict between the '
                . 'factions.',
            $playbook->description_long,
        );
        self::assertSame(
            'a powerful warrior devoted to what they think is right and just.',
            $playbook->description_short,
        );
    }

    public function testNatures(): void
    {
        $playbook = Playbook::findOrFail('arbiter');
        self::assertEquals(
            [
                'defender' => Nature::find('defender'),
                'punisher' => Nature::find('punisher'),
            ],
            $playbook->natures,
        );
    }

    public function testMoves(): void
    {
        $playbook = Playbook::findOrFail('arbiter');
        self::assertEquals(
            [
                'brute' => Move::find('brute'),
                'carry-a-big-stick' => Move::find('carry-a-big-stick'),
            ],
            $playbook->moves,
        );
    }

    public function testStartingWeaponMoves(): void
    {
        $playbook = Playbook::findOrFail('arbiter');
        self::assertCount(4, $playbook->starting_weapon_moves);
        self::assertEquals(
            Move::findOrFail('cleave'),
            $playbook->starting_weapon_moves->first(),
        );
    }
}
