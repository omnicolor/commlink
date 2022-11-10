<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Card;
use RuntimeException;

/**
 * Representation of a Virtue from the Capers RPG.
 *
 * Your character’s Virtue is their most morally commendable characteristic.
 * It’s something that others respect and look up to them for. It’s a quality
 * they never betray no matter the cost.
 *
 * You can gain Moxie if your character stays true to their Virtue when it would
 * be easier to ignore it to accomplish something.
 */
class Virtue
{
    public string $card;
    public string $description;
    public string $id;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static array $virtues;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.capers') . 'virtues.php';
        self::$virtues ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$virtues[$this->id])) {
            throw new RuntimeException(
                \sprintf('Virtue ID "%s" is invalid', $id)
            );
        }

        $virtue = self::$virtues[$this->id];
        $this->card = $virtue['card'];
        $this->description = $virtue['description'];
        $this->name = $virtue['name'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all virtues.
     * @return array<string, Virtue>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.capers') . 'virtues.php';
        self::$virtues ??= require $filename;

        $virtues = [];
        foreach (self::$virtues as $id => $virtue) {
            $virtues[(string)$id] = new self($id);
        }
        return $virtues;
    }

    public static function findForCard(Card $card): Virtue
    {
        $filename = config('app.data_path.capers') . 'virtues.php';
        self::$virtues ??= require $filename;

        foreach (self::$virtues as $id => $virtue) {
            if ($virtue['card'] === $card->value) {
                return new self($id);
            }
        }

        throw new RuntimeException(sprintf(
            'Virtue not found for %s',
            (string)$card
        ));
    }
}
