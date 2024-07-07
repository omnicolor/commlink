<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

class PartialCharacter extends Character
{
    /**
     * Table to pull from.
     * @var string
     */
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
    public function toCharacter(): Character
    {
        $rawCharacter = $this->toArray();
        unset($rawCharacter['_id']);
        return new Character($rawCharacter);
    }
}
