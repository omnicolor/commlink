<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use Illuminate\Database\Eloquent\Attributes\Connection;
use Illuminate\Database\Eloquent\Attributes\Table;
use Override;
use Stringable;

#[Connection('mongodb')]
#[Table(name: 'characters-partial')]
class PartialCharacter extends Character implements Stringable
{
    /** @var array<string, array<int, string>> */
    public array $errors = [];

    #[Override]
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
