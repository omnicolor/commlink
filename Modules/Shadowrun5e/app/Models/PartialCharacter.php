<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Shadowrun5e\Database\Factories\PartialCharacterFactory;
use Stringable;

/**
 * Representation of a character currently being built.
 * @property array<int, string> $errors
 */
class PartialCharacter extends Character implements Stringable
{
    protected const int DEFAULT_MAX_ATTRIBUTE = 6;

    /**
     * The database connection that should be used by the model.
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * Table to pull from.
     * @var string
     */
    protected $table = 'characters-partial';

    /**
     * Return the starting maximum for a character based on their metatype and
     * qualities.
     * @psalm-suppress UndefinedThisPropertyFetch
     */
    public function getStartingMaximumAttribute(string $attribute): int
    {
        $maximums = [
            'dwarf' => [
                'body' => 8,
                'reaction' => 5,
                'strength' => 8,
                'willpower' => 7,
            ],
            'elf' => [
                'agility' => 7,
                'charisma' => 8,
            ],
            'human' => [
                'edge' => 7,
            ],
            'ork' => [
                'body' => 9,
                'charisma' => 5,
                'logic' => 5,
                'strength' => 8,
            ],
            'troll' => [
                'agility' => 5,
                'body' => 10,
                'charisma' => 4,
                'intuition' => 5,
                'logic' => 5,
                'strength' => 10,
            ],
        ];
        $max = $maximums[$this->metatype][$attribute] ?? self::DEFAULT_MAX_ATTRIBUTE;
        foreach ($this->getQualities() as $quality) {
            if (!isset($quality->effects['maximum-' . $attribute])) {
                continue;
            }
            $max += $quality->effects['maximum-' . $attribute];
        }
        return $max;
    }

    /**
     * Return whether the given character is awakened and not a techno.
     */
    public function isMagicallyActive(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            // @phpstan-ignore-next-line
            && null !== $this->priorities['magic']
            && 'technomancer' !== $this->priorities['magic'];
    }

    /**
     * Return whether the character is a technomancer.
     */
    public function isTechnomancer(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            // @phpstan-ignore-next-line
            && null !== $this->priorities['magic']
            && 'technomancer' === $this->priorities['magic'];
    }

    protected static function newFactory(): Factory
    {
        // @phpstan-ignore-next-line
        return PartialCharacterFactory::new();
    }

    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): PartialCharacter {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        $character->fillable[] = 'errors';
        // @phpstan-ignore-next-line
        return $character;
    }
}
