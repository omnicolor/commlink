<?php

declare(strict_types=1);

namespace App\Models\Capers;

/**
 * Representation of a character currently being built.
 * @property array<int, object> $gear
 * @property array<string, string> $meta
 * @property object $powers
 */
class PartialCharacter extends Character
{
    /**
     * The database connection that should be used by the model.
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * @var array<int, string>
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
