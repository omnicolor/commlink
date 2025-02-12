<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transformers\Database\Factories\PartialCharacterFactory;

/**
 * Representation of a character currently being built.
 * @method static self create(array<mixed, mixed> $attributes)
 * @property array<int, string> $errors
 */
class PartialCharacter extends Character
{
    /**
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * @var string
     */
    protected $table = 'characters-partial';

    public function newFromBuilder(
        // @phpstan-ignore parameter.defaultValue
        $attributes = [],
        $connection = null,
    ): PartialCharacter {
        $character = new self((array)$attributes);
        $character->exists = true;
        $character->setRawAttributes((array)$attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        $character->fillable[] = 'errors';
        // @phpstan-ignore return.type
        return $character;
    }

    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }
}
