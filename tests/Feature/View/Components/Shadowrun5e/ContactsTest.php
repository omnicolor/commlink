<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Contacts;

/**
 * @small
 */
final class ContactsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of contacts for an existing character.
     * @test
     */
    public function testNoContactsExistingCharacter(): void
    {
        $this->component(Contacts::class, ['character' => new Character()])
            ->assertDontSee('contacts', false)
            ->assertDontSee('Character does not know anyone.');
    }

    /**
     * Test rendering a lack of contacts for a new character.
     * @test
     */
    public function testNoContactsNewCharacter(): void
    {
        $this->component(Contacts::class, ['character' => new PartialCharacter()])
            ->assertSee('contacts')
            ->assertSee('Character does not know anyone.');
    }

    /**
     * Test rendering a contact.
     * @test
     */
    public function testContacts(): void
    {
        $this->component(
            Contacts::class,
            [
                'character' => new Character([
                    'contacts' => [
                        [
                            'name' => 'Bob King',
                            'archetype' => 'Fixer',
                            'connection' => 1,
                            'loyalty' => 2,
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('contacts')
            ->assertSee('Bob King')
            ->assertDontSee('Character does not know anyone.');
    }
}
