<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Card;
use RuntimeException;

/**
 * Your character’s Vice is their greatest weakness. It regularly causes
 * problems for them and takes a long time to overcome, if they manage to
 * overcome it at all.
 *
 * You can gain Moxie if your character is hindered by their Vice.
 */
class Vice
{
    public string $card;
    public string $description;
    public string $id;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static array $vices;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.capers') . 'vices.php';
        self::$vices ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$vices[$this->id])) {
            throw new RuntimeException(
                \sprintf('Vice ID "%s" is invalid', $id)
            );
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
        foreach (self::$vices as $id => $vice) {
            $vices[(string)$id] = new self($id);
        }
        return $vices;
    }

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
