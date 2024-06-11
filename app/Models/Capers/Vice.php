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
 * Your character’s Vice is their greatest weakness. It regularly causes
 * problems for them and takes a long time to overcome, if they manage to
 * overcome it at all.
 *
 * You can gain Moxie if your character is hindered by their Vice.
 */
class Vice implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $card;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static array $vices;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.capers') . 'vices.php';
        self::$vices ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$vices[$this->id])) {
            throw new RuntimeException(sprintf('Vice ID "%s" is invalid', $id));
        }

        $vice = self::$vices[$this->id];
        $this->card = $vice['card'];
        $this->description = $vice['description'];
        $this->name = $vice['name'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return all vices.
     * @return array<string, Vice>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.capers') . 'vices.php';
        self::$vices ??= require $filename;

        $vices = [];
        /** @var string $id */
        foreach (array_keys(self::$vices) as $id) {
            $vices[$id] = new self($id);
        }
        return $vices;
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public static function findForCard(Card $card): Vice
    {
        $filename = config('app.data_path.capers') . 'vices.php';
        self::$vices ??= require $filename;

        foreach (self::$vices as $id => $vice) {
            if ($vice['card'] === $card->value) {
                return new self($id);
            }
        }

        throw new RuntimeException(sprintf(
            'Vice not found for %s',
            (string)$card
        ));
    }
}
