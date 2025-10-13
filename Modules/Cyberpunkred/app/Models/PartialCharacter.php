<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use Facades\App\Services\DiceService;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Cyberpunkred\Database\Factories\PartialCharacterFactory;
use Override;
use Stringable;

/**
 * Representation of a character currently being built.
 * @method static self create(array<mixed, mixed> $attributes)
 * @property array<string, array{rolled?: int, chosen: int}> $lifepath
 */
class PartialCharacter extends Character implements Stringable
{
    /** @var string */
    protected $connection = 'mongodb';

    /** @var string */
    protected $table = 'characters-partial';

    /** @var array<string, array<int, string>> */
    public array $errors = [];

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
        // @phpstan-ignore return.type
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
        // @phpstan-ignore assign.propertyType
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
        $value = DiceService::rollOne(10);
        return [
            'rolled' => $value,
            'chosen' => $value,
        ];
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }
}
