<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * Representation of a character in character generation.
 * @property-read int $fortune
 */
class PartialCharacter extends Character
{
    public const STARTING_FORTUNE = 320;
    public const CORRUPTED_VALUE_FORTUNE = 5;

    protected $connection = 'mongodb';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agility',
        'arts',
        'awareness',
        'background',
        'brawn',
        'campaign_id',
        'caste',
        'charisma',
        'corrupted_value',
        'dulled',
        'gear',
        'ideology',
        'impulse',
        'languages',
        'lineage',
        'lineage_option',
        'name',
        'origin',
        'owner',
        'skills',
        'system',
        'values',
        'will',
        'wit',
    ];

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
    public function fortune(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $fortune = self::STARTING_FORTUNE;
                if (null !== $this->caste) {
                    $fortune += $this->caste->fortune;
                }
                if (true === $this->corrupted_value) {
                    $fortune += self::CORRUPTED_VALUE_FORTUNE;
                }
                return $fortune;
            },
        );
    }
}
