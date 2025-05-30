<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Transformers\Database\Factories\PartialCharacterFactory;
use Override;

/**
 * Representation of a character currently being built.
 * @method static self create(array<mixed, mixed> $attributes)
 * @property array<int, string> $errors
 */
class PartialCharacter extends Character
{
    /** @var string */
    protected $table = 'characters-partial';

    #[Override]
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

    #[Override]
    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }
}
