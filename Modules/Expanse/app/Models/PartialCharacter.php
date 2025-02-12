<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use Stringable;

class PartialCharacter extends Character implements Stringable
{
    /**
     * The database connection that should be used by the model.
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * Table to pull from.
     * @var string
     */
    protected $table = 'characters-partial';

    /**
     * @var array<int, string>
     */
    public array $errors = [];

    public function newFromBuilder(
        // @phpstan-ignore parameter.defaultValue
        $attributes = [],
        $connection = null,
    ): self {
        $character = new self((array)$attributes);
        $character->exists = true;
        $character->setRawAttributes((array)$attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $character;
    }
}
