<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Contacts, because it's all about who you know.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Contact
{
    /**
     * Archetype for the contact.
     */
    public string $archetype;

    /**
     * Numerical rating for how connected the contact is in the world.
     */
    public ?int $connection;

    /**
     * Characters that the contact shares.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, array<string, int|null|string>>
     */
    public array $characters = [];

    /**
     * Notes the GM has made about the contect, not shown to players.
     */
    public ?string $gmNotes;

    /**
     * Numerical rating for how loyal the contact is to the character.
     */
    public ?int $loyalty;

    /**
     * Contact's street name.
     */
    public string $name;

    /**
     * Notes about the contact.
     */
    public ?string $notes;

    /**
     * Construct a new contact object.
     * @param array<string, mixed> $data
     */
    public function __construct(array $data)
    {
        $this->archetype = $data['archetype'];
        $this->connection = $data['connection'];
        $this->gmNotes = $data['gmNotes'] ?? '';
        $this->loyalty = $data['loyalty'];
        $this->name = $data['name'];
        $this->notes = $data['notes'] ?? '';
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a collection of sample archetypes.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, string>
     */
    public static function archetypes(): array
    {
        return [
            'Ambulance driver',
            'Arms Dealer',
            'Bartender',
            'Beat cop',
            'Border patrol agent',
            'Bouncer',
            'Company man',
            'Corporate wagemage',
            'Coyote',
            'Decker',
            'Detective',
            'EMT',
            'Fench',
            'Fixer',
            'Forger',
            'Gang boss',
            'Government agent',
            'Loan Shark',
            'Mafia consigliere',
            'Mechanic',
            'Mr. Johnson',
            'Nightclub owner',
            'Nurse',
            'Rent-a-cop',
            'Reporter',
            'Rigger',
            'Shadowrunner',
            'Simsense Star',
            'Smuggler',
            'Snitch',
            'Street doc',
            'Street mage',
            'Street Sam',
            'Street Shaman',
            'Stripper',
            'Talismonger',
            'Tech wizard',
            'Triad incense aster',
            'Trid actor',
            'Undercover cop',
            'Waitress',
            'Yakuza wakagashira',
        ];
    }
}
