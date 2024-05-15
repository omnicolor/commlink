<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Representation of a character in character generation.
 */
class PartialCharacter extends Character
{
    public const STARTING_FORTUNE = 320;

    protected $connection = 'mongodb';

    protected $table = 'characters-partial';

    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): PartialCharacter {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore-next-line
        return $character;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function fortune(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return self::STARTING_FORTUNE;
            },
        );
    }
}
