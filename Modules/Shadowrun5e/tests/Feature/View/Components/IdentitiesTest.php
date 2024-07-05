<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\View\Components\Identities;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class IdentitiesTest extends TestCase
{
    /**
     * Test no identities for an existing character.
     */
    public function testNoIdentitiesExistingCharacter(): void
    {
        $this->component(Identities::class, ['character' => new Character()])
            ->assertDontSee('identities')
            ->assertDontSee('Character has no identities');
    }

    /**
     * Test no identities for a new character.
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
