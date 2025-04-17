<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use LogicException;
use Modules\Stillfleet\Models\Character;
use Modules\Stillfleet\Models\Power;
use Modules\Stillfleet\Models\Role;
use Modules\Stillfleet\Models\Species;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class CharacterTest extends TestCase
{
    public function testConvertNotEnoughHealth(): void
    {
        $character = new Character([
            'combat' => 'd6',
            'health_current' => 2,
            'movement' => 'd10',
            'reason' => 'd4',
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Not enough health to convert');
        $character->convert();
    }

    public function testConvert(): void
    {
        $character = new Character([
            'combat' => 'd6',
            'movement' => 'd10',
            'reason' => 'd4',
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::assertSame(14, $character->grit);
        self::assertSame(14, $character->grit_current);
        self::assertSame(16, $character->health);
        self::assertSame(16, $character->health_current);

        $character->grit_current = 9;
        $character->health_current = 10;
        $character->convert();

        self::assertSame(10, $character->grit_current);
        self::assertSame(7, $character->health_current);
    }

    public function testGrit(): void
    {
        // Banshee's grit is movement + reason.
        $character = new Character([
            'movement' => 'd10',
            'reason' => 'd4',
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);
        self::assertSame(14, $character->grit);
        $character->movement = 'd12';
        self::assertSame(16, $character->grit);
        $character->reason = 'd6';
        self::assertSame(18, $character->grit);
    }

    public function testGritWithNegative(): void
    {
        // Tremulant's grit is (maxREA + maxWIL + maxCHA) − maxCOM
        $character = new Character([
            'charm' => 'd8',
            'combat' => 'd10',
            'reason' => 'd4',
            'will' => 'd6',
            'roles' => [['id' => 'tremulant', 'level' => 1]],
        ]);
        self::assertSame(8, $character->grit);
    }

    public function testWriteGrit(): void
    {
        $character = new Character([
            'combat' => 6,
            'movement' => 10,
            'reason' => 4,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::expectException(LogicException::class);
        $character->grit = 10;
    }

    public function testGritWithPower(): void
    {
        $character = new Character([
            'species' => 'fleeter',
        ]);
        // @phpstan-ignore method.nonObject
        $character->species->addPowers(Power::find('auxin-network-non-mind-plant-mind'));
        self::assertSame(4, $character->grit);
    }

    public function testHealth(): void
    {
        $character = new Character(['combat' => 'd4', 'movement' => 'd4']);
        self::assertSame(8, $character->health);
        self::assertSame(8, $character->health_current);
        $character->combat = 'd6';
        self::assertSame(10, $character->health);
        $character->movement = 'd8';
        self::assertSame(14, $character->health);
    }

    public function testRoleInvalid(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'id' => 'invalid',
                    'level' => 1,
                ],
            ],
        ]);
        self::expectException(ModelNotFoundException::class);
        // @phpstan-ignore expr.resultUnused
        $character->roles;
    }

    public function testRoles(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => ['astrogate'],
                ],
            ],
        ]);

        self::assertInstanceOf(Role::class, $character->roles[0]);
    }

    public function testSpecies(): void
    {
        $character = new Character(['species' => 'fleeter']);
        self::assertSame('Fleeter', (string)$character->species);
    }

    public function testSpeciesReasonModifier(): void
    {
        $character = new Character(['species' => 'fleeter']);
        self::assertSame(0, $character->reason_modifier);
        $character->species?->addPowers(Power::findOrFail('arkheion-access'));
        self::assertSame(1, $character->reason_modifier);
    }

    public function testSpeciesWithChosenPower(): void
    {
        $character = new Character([
            'species' => 'fleeter',
            'species_powers' => ['arkheion-access'],
        ]);
        self::assertCount(4, $character->species->powers ?? []);
    }

    public function testSpeciesWithInvalidChosenPower(): void
    {
        $character = new Character([
            'species' => 'fleeter',
            'species_powers' => ['invalid'],
        ]);
        self::assertCount(3, $character->species->powers ?? []);
    }

    public function testSetSpeciesObject(): void
    {
        $character = new Character(['species' => 'fleeter']);
        $character->species = Species::findOrFail('shoodtha');
        self::assertSame('shoodtha', $character->species?->id);
    }

    public function testSetSpeciesString(): void
    {
        $character = new Character(['species' => 'fleeter']);
        $character->species = 'shoodtha';
        self::assertSame('shoodtha', $character->species?->id);
    }

    public function testAllPowers(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                    'powers' => ['astrogate'],
                ],
            ],
            'species' => 'fleeter',
            'species_powers' => ['arkheion-access'],
        ]);
        self::assertCount(8, $character->all_powers);
    }
}
