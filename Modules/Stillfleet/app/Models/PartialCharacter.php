<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use App\Services\DiceService;
use Override;
use RuntimeException;

/**
 * @method static self create(array<mixed, mixed> $attributes)
 * @property string $attribute_dice_option
 */
class PartialCharacter extends Character
{
    /**
     * @var string
     */
    protected $table = 'characters-partial';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'attribute_dice_option',
        'charm',
        'combat',
        'grit_current',
        'health_current',
        'hustle',
        'kin',
        'languages',
        'money',
        'movement',
        'name',
        'origin',
        'owner',
        'rank',
        'reason',
        'roles', // Classes in the rules.
        'species',
        'species_powers',
        'teloi',
        'will',
    ];

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

    public function toCharacter(): Character
    {
        $rawCharacter = $this->toArray();
        unset($rawCharacter['_id']);
        return new Character($rawCharacter);
    }

    public function startingMoney(): int
    {
        if (null === $this->will || null === $this->charm) {
            throw new RuntimeException('Character must have attributes set');
        }

        return 10 * (
            DiceService::rollMax($this->charm)
            + DiceService::rollMax($this->will)
        );
    }
}
