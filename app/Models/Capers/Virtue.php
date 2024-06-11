<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Card;
use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

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
class Virtue implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $card;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static array $virtues;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.capers') . 'virtues.php';
        self::$virtues ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$virtues[$this->id])) {
            throw new RuntimeException(
                sprintf('Virtue ID "%s" is invalid', $id)
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
        /** @var string $id */
        foreach (array_keys(self::$virtues) as $id) {
            $virtues[$id] = new self($id);
        }
        return $virtues;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
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
