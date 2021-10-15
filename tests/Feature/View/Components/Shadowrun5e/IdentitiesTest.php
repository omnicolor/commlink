<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\View\Components\Shadowrun5e\Identities;

/**
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class IdentitiesTest extends \Tests\TestCase
{
    /**
     * Test no identities for an existing character.
     * @test
     */
    public function testNoIdentitiesExistingCharacter(): void
    {
        $this->component(Identities::class, ['character' => new Character()])
            ->assertDontSee('identities')
            ->assertDontSee('Character has no identities');
    }

    /**
     * Test no identities for a new character.
     * @test
     */
    public function testNoIdentitiesNewCharacter(): void
    {
        $this->component(
            Identities::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('identities')
            ->assertSee('Character has no identities');
    }

    /**
     * Test rendering an identity.
     * @test
     */
    public function testIdentity(): void
    {
        $this->component(
            Identities::class,
            [
                'character' => new Character([
                    'identities' => [
                        [
                            'id' => 0,
                            'name' => 'Fake Name',
                            'sin' => 3,
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('identities')
            ->assertDontSee('Character has no identities')
            ->assertSee('Fake Name')
            ->assertSee('Fake SIN (3)');
    }
}
