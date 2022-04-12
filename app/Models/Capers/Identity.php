<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Card;
use RuntimeException;

/**
 * Representation of an Identity from the Capers RPG.
 *
 * Your character’s Identity is who your character is at their core. It’s a
 * guiding principle for your roleplaying. It’s a term that other characters
 * might use when describing your character’s actions and beliefs.
 *
 * But it’s not everything. Your character can have many facets to their
 * personality. But, when in doubt about how your character might react to a
 * situation, their Identity can make for a pretty good guide.
 *
 * And keep in mind your character’s Identity might change over time. Actions
 * have consequences. If your character experiences a trauma or a great victory
 * or something else that changes their viewpoint on life and themselves, change
 * their Identity.
 *
 * You can gain Moxie if your character stays consistent to their Identity.
 */
class Identity
{
    public string $card;
    public string $description;
    public string $id;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static array $identities;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.capers') . 'identities.php';
        self::$identities ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$identities[$this->id])) {
            throw new RuntimeException(
                \sprintf('Identity ID "%s" is invalid', $id)
            );
        }

        $identity = self::$identities[$this->id];
        $this->card = $identity['card'];
        $this->description = $identity['description'];
        $this->name = $identity['name'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all identities.
     * @return array<string, Identity>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.capers') . 'identities.php';
        self::$identities ??= require $filename;

        $identities = [];
        foreach (self::$identities as $id => $identity) {
            $identities[$id] = new self($id);
        }
        return $identities;
    }

    public static function findForCard(Card $card): Identity
    {
        $filename = config('app.data_path.capers') . 'identities.php';
        self::$identities ??= require $filename;

        $coloredCard = match ($card->suit) {
            '♣' => 'B' . $card->value,
            '♦' => 'R' . $card->value,
            '♥' => 'R' . $card->value,
            '♠' => 'B' . $card->value,
            '' => throw new RuntimeException('Joker drawn, draw again'),
            default => throw new RuntimeException('Invalid suit'),
        };

        foreach (self::$identities as $id => $identity) {
            if ($identity['card'] === $coloredCard) {
                return new self($id);
            }
        }

        throw new RuntimeException(sprintf(
            'Identity not found for %s',
            (string)$card
        ));
    }
}
