<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Contacts, because it's all about who you know.
 */
class Contact
{
    /**
     * Archetype for the contact.
     * @var string
     */
    public string $archetype;

    /**
     * Numerical rating for how connected the contact is in the world.
     * @var int
     */
    public ?int $connection;

    /**
     * Notes the GM has made about the contect, not shown to players.
     * @var string
     */
    public ?string $gmNotes;

    /**
     * Numerical rating for how loyal the contact is to the character.
     * @var ?int
     */
    public ?int $loyalty;

    /**
     * Contact's street name.
     * @var string
     */
    public string $name;

    /**
     * Notes about the contact.
     * @var string
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

    /**
     * Return the contact as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a collection of sample archetypes.
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
