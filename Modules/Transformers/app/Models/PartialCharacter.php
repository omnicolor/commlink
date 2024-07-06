<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transformers\Database\Factories\PartialCharacterFactory;

/**
 * Representation of a character currently being built.
 * @property array<int, string> $errors
 */
class PartialCharacter extends Character
{
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

    public function newFromBuilder(
        $attributes = [],
        $connection = null
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

    protected static function newFactory(): Factory
    {
        // @phpstan-ignore-next-line
        return PartialCharacterFactory::new();
    }
}
