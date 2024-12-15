<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

/**
 * @method static self create(array<mixed, mixed> $attributes)
 */
class PartialCharacter extends Character
{
    /**
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
        // @phpstan-ignore return.type
        return $character;
    }

    public function toCharacter(): Character
    {
        $rawCharacter = $this->toArray();
        unset($rawCharacter['_id']);
        return new Character($rawCharacter);
    }
}
