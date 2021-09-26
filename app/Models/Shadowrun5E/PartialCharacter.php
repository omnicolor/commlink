<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Representation of a character currently being built.
 */
class PartialCharacter extends Character
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

    // @phpstan-ignore-next-line
    public function newFromBuilder(
        $attributes = [],
        $connection = null
    ): PartialCharacter {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        return $character;
    }

    /**
     * Return whether the given character is awakened and not a techno.
     * @return bool
     */
    public function isMagicallyActive(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            && null !== $this->priorities['magic']
            && 'technomancer' !== $this->priorities['magic'];
    }

    /**
     * Return whether the character is a technomancer.
     * @return bool
     */
    public function isTechnomancer(): bool
    {
        return isset($this->priorities, $this->priorities['magic'])
            && null !== $this->priorities['magic']
            && 'technomancer' === $this->priorities['magic'];
    }
}
