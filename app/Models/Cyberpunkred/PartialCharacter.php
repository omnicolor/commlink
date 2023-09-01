<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

/**
 * Representation of a character currently being built.
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

    /**
     * @var array<int, string>
     */
    public array $errors = [];

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
     * Initialize a character's lifepath section.
     */
    public function initializeLifepath(): void
    {
        if (isset($this->lifepath)) {
            return;
        }
        $this->lifepath = [
            'affectation' => $this->createSubLifepathValue(),
            'background' => $this->createSubLifepathValue(),
            'clothing' => $this->createSubLifepathValue(),
            'environment' => $this->createSubLifepathValue(),
            'feeling' => $this->createSubLifepathValue(),
            'hair' => $this->createSubLifepathValue(),
            'origin' => $this->createSubLifepathValue(),
            'person' => $this->createSubLifepathValue(),
            'personality' => $this->createSubLifepathValue(),
            'possession' => $this->createSubLifepathValue(),
            'value' => $this->createSubLifepathValue(),
        ];
        $this->update();
    }

    /**
     * Store both what we roll for the user and what they choose for the value.
     * @return array<string, int>
     */
    protected function createSubLifepathValue(): array
    {
        $value = random_int(1, 10);
        return [
            'rolled' => $value,
            'chosen' => $value,
        ];
    }
}
