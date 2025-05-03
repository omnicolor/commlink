<?php

declare(strict_types=1);

namespace Modules\Battletech\ValueObjects;

use DomainException;

/**
 * @phpstan-type AttributesArray array{
 *     body: int,
 *     charisma: int,
 *     dexterity: int,
 *     edge: int,
 *     intelligence: int,
 *     reflexes: int,
 *     strength: int,
 *     willpower: int
 * }
 */
final class Attributes
{
    public function __construct(
        public Attribute $body,
        public Attribute $charisma,
        public Attribute $dexterity,
        public Attribute $edge,
        public Attribute $intelligence,
        public Attribute $reflexes,
        public Attribute $strength,
        public Attribute $willpower,
    ) {
    }

    /**
     * @param AttributesArray $attributes
     */
    public static function make(array $attributes): self
    {
        if (
            !isset(
                $attributes['body'],
                $attributes['charisma'],
                $attributes['dexterity'],
                $attributes['edge'],
                $attributes['intelligence'],
                $attributes['reflexes'],
                $attributes['strength'],
                $attributes['willpower'],
            )
        ) {
            throw new DomainException('Attributes list is incomplete.');
        }

        return new self(
            body: new Attribute($attributes['body']),
            charisma: new Attribute($attributes['charisma']),
            dexterity: new Attribute($attributes['dexterity']),
            edge: new Attribute($attributes['edge']),
            intelligence: new Attribute($attributes['intelligence']),
            reflexes: new Attribute($attributes['reflexes']),
            strength: new Attribute($attributes['strength']),
            willpower: new Attribute($attributes['willpower']),
        );
    }

    /**
     * @return AttributesArray
     */
    public function toArray(): array
    {
        return [
            'body' => $this->body->value,
            'charisma' => $this->charisma->value,
            'dexterity' => $this->dexterity->value,
            'edge' => $this->edge->value,
            'intelligence' => $this->intelligence->value,
            'reflexes' => $this->reflexes->value,
            'strength' => $this->strength->value,
            'willpower' => $this->willpower->value,
        ];
    }
}
