<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Alien\Database\Factories\PartialCharacterFactory;
use Stringable;

use function array_key_exists;
use function sprintf;

/**
 * @property-read ?Career $career
 * @property-write Career|string $career
 */
class PartialCharacter extends Character implements Stringable
{
    use HasFactory;

    protected $table = 'characters-partial';

    public function __toString(): string
    {
        return $this->name ?? 'Unfinished character';
    }

    protected static function newFactory(): Factory
    {
        return PartialCharacterFactory::new();
    }

    public function newFromBuilder($attributes = [], $connection = null): self
    {
        $character = new self($attributes);
        $character->exists = true;
        $character->setRawAttributes($attributes, true);
        $character->setConnection($this->connection);
        $character->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $character;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function toCharacter(): Character
    {
        $character = $this->toArray();
        $character['armor'] = $this->armor?->id;
        $character['career'] = $this->career?->id;
        $character['gear'] = [];
        foreach (collect($this->gear) as $item) {
            $character['gear'][] = [
                'id' => $item->id,
                'quantity' => $item->quantity ?? 1,
            ];
        }
        $character['weapons'] = [];
        foreach (collect($this->weapons) as $weapon) {
            $character['weapons'][] = $weapon->id;
        }
        $character['skills'] = collect($this->skills)
            ->pluck('rank', 'id')
            ->toArray();
        $character['talents'] = [$this->talents[0]->id];
        return new Character($character);
    }

    /**
     * @return array<string, string>
     */
    public function validate(): array
    {
        $errors = [];
        if (null === $this->career) {
            $errors['career'] = 'You haven\'t chosen a career';
        } else {
            $careerSkills = collect($this->career->keySkills)->pluck('id');
            $skills = collect($this->skills);
            foreach ($skills as $id => $skill) {
                if ($careerSkills->contains($id)) {
                    if (3 < $skill->rank) {
                        $errors['skill-' . $id] = sprintf(
                            'Your rank in skill "%s" is too high',
                            $skill->name,
                        );
                        continue;
                    }
                    continue;
                }
                if (1 < $skill->rank) {
                    $errors['skill-' . $id] = sprintf(
                        'Your rank in skill "%s" is too high',
                        $skill->name,
                    );
                }
            }
        }
        if (!array_key_exists('name', $this->attributes)) {
            $errors['name'] = 'Your character has no name';
        }
        if (
            !array_key_exists('agility', $this->attributes)
            || !array_key_exists('empathy', $this->attributes)
            || !array_key_exists('strength', $this->attributes)
            || !array_key_exists('wits', $this->attributes)
        ) {
            $errors['attributes'] = 'You have not set your attributes';
        } else {
            $attributes = $this->attributes['agility']
                + $this->attributes['empathy'] + $this->attributes['strength']
                + $this->attributes['wits'];
            if (14 < $attributes) {
                $errors['attributes']
                    = 'You haven spent too many attribute points';
            } elseif (14 > $attributes) {
                $errors['attributes']
                    = 'You haven\'t spent all of your attribute points';
            }
            $attributes = ['agility', 'empathy', 'strength', 'wits'];
            foreach ($attributes as $attribute) {
                if (2 > $this->attributes[$attribute]) {
                    $errors[$attribute] = sprintf(
                        'Your %s must be 2 or higher',
                        $attribute,
                    );
                    continue;
                }
                if ($attribute === $this->career?->keyAttribute) {
                    if (5 < $this->attributes[$attribute]) {
                        $errors[$attribute] = sprintf(
                            'Your %s must be 5 or lower',
                            $attribute,
                        );
                    }
                    continue;
                }
                if (4 < $this->attributes[$attribute]) {
                    $errors[$attribute] = sprintf(
                        'Your %s must be 4 or lower',
                        $attribute,
                    );
                }
            }
        }

        $skills = collect($this->skills)->pluck('rank')->sum();
        if (10 > $skills) {
            $errors['skills'] = 'You haven\'t spent all of your skill points';
        } elseif (10 < $skills) {
            $errors['skills'] = 'You have spent too many skill points';
        }

        if (1 !== count($this->talents)) {
            $errors['talent'] = 'You haven\'t chosen a talent';
        }

        $gear = count($this->gear) + count($this->weapons)
            + (null !== $this->armor ? 1 : 0);
        if (2 !== $gear) {
            $errors['gear'] = 'You haven\'t chosen your gear';
        }

        return $errors;
    }
}
