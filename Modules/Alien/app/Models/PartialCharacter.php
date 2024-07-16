<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Alien\Database\Factories\PartialCharacterFactory;
use Stringable;

/**
 * @property-read ?Career $career
 * @property-write Career|string $career
 */
class PartialCharacter extends Character implements Stringable
{
    use HasFactory;

    protected $table = 'characters-partial';

    public function __toString(): string
    {
        return $this->name ?? 'Unfinished character';
    }

    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }

    public function newFromBuilder($attributes = [], $connection = null): self
    {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $character;
    }
}
