<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Contacts;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class ContactsTest extends TestCase
{
    /**
     * Test rendering a lack of contacts for an existing character.
     */
    public function testNoContactsExistingCharacter(): void
    {
        self::component(Contacts::class, ['character' => new Character()])
            ->assertDontSee('contacts', false)
            ->assertDontSee('Character does not know anyone.');
    }

    /**
     * Test rendering a lack of contacts for a new character.
     */
    public function testNoContactsNewCharacter(): void
    {
        self::component(Contacts::class, ['character' => new PartialCharacter()])
            ->assertSee('contacts')
            ->assertSee('Character does not know anyone.');
    }

    /**
     * Test rendering a contact.
     */
    public function testContacts(): void
    {
        self::component(
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
