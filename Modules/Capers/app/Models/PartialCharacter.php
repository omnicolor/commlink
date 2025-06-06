<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Capers\Database\Factories\PartialCharacterFactory;
use Override;
use Stringable;

use function optional;

/**
 * Representation of a character currently being built.
 * @method static self create(array<mixed, mixed> $attributes)
 * @property-write array<int, array<string, mixed>> $gear
 * @property array<string, string> $meta
 */
class PartialCharacter extends Character implements Stringable
{
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'agility',
        'background',
        'charisma',
        'description',
        'expertise',
        'gear',
        'hits',
        'identity',
        'level',
        'mannerisms',
        'meta', // Information only needed during chargen.
        'moxie',
        'name',
        'owner',
        'perception',
        'perks',
        'powers',
        'resilience',
        'skills',
        'strength',
        'type',
        'vice',
        'virtue',
    ];

    /**
     * Table to pull from.
     * @var string
     */
    protected $table = 'characters-partial';

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'New Character';
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }

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
        $rawCharacter['identity'] = optional($this->identity)->id;
        $rawCharacter['vice'] = optional($this->vice)->id;
        $rawCharacter['virtue'] = optional($this->virtue)->id;

        $skills = [];
        foreach ($this->skills as $skill) {
            $skills[] = $skill->id;
        }
        $rawCharacter['skills'] = $skills;

        $powers = [];
        foreach ($this->powers as $power) {
            $boosts = [];
            foreach ($power->boosts as $boost) {
                $boosts[] = $boost->id;
            }
            $powers[$power->id] = [
                'boosts' => $boosts,
                'id' => $power->id,
                'rank' => $power->rank,
            ];
        }
        $rawCharacter['powers'] = $powers;

        $gear = [];
        foreach ($this->gear as $item) {
            $gear[] = [
                'id' => $item->id,
                'quantity' => $item->quantity,
            ];
        }
        $rawCharacter['gear'] = $gear;

        return new Character($rawCharacter);
    }
}
