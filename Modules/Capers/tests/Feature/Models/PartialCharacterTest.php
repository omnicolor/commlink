<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\PartialCharacter;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('capers')]
#[Small]
final class PartialCharacterTest extends TestCase
{
    public function testNewFromBuilder(): void
    {
        $character = new PartialCharacter(['name' => 'Test Capers character']);
        $character->save();

        $loaded = PartialCharacter::find($character->id);
        // @phpstan-ignore-next-line
        self::assertSame('Test Capers character', $loaded->name);
        $character->delete();
    }

    /**
     * Test converting a partial character to a real character.
     */
    public function testToCharacter(): void
    {
        /** @var PartialCharacter */
        $partial = PartialCharacter::create([
            'background' => 'Detailed backstory.',
            'description' => 'A bit insane.',
            'mannerisms' => 'Eccentric and pompous.',
            'name' => 'Caper Character',
            'type' => 'caper',
            'identity' => 'pushover',
            'vice' => 'phobia',
            'virtue' => 'respectful',
            'agility' => 2,
            'charisma' => 2,
            'expertise' => 3,
            'perception' => 2,
            'resilience' => 2,
            'strength' => 1,
            'skills' => [
                'guns',
                'humanities',
                'mechanicals',
                'sciences',
                'sense',
            ],
            'meta' => [
                'powers-option' => 'two-minor',
            ],
            'powers' => [
                'alter-form' => [
                    'boosts' => [
                        'gaseous-form-boost',
                        'immovability-boost',
                        'liquid-form-boost',
                    ],
                    'id' => 'alter-form',
                    'rank' => 1,
                ],
                'bone-organ-shifting' => [
                    'boosts' => [
                        'bone-armor-boost',
                        'bone-spurs-boost',
                        'organ-shift-boost',
                    ],
                    'id' => 'bone-organ-shifting',
                    'rank' => 1,
                ],
            ],
            'gear' => [
                [
                    'id' => 'mens-boots',
                    'quantity' => 1,
                ],
            ],
        ]);

        $character = $partial->toCharacter();
        self::assertCount(5, $character->skills);
        self::assertCount(2, $character->powers);
        self::assertCount(1, $character->gear);
        $partial->delete();
    }
}
