<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Illuminate\Support\Str;
use Stringable;

use function ceil;
use function count;
use function debug_backtrace;
use function max;
use function sprintf;
use function trigger_error;

use const E_USER_NOTICE;

/**
 * @property-read int $cost
 */
class Relation implements Stringable
{
    /**
     * @param array<int, Skill> $skills
     * @param array<int, RelationArchetype> $archetypes
     * @param array<int, RelationAspect> $aspects
     */
    public function __construct(
        public string $name,
        public array $skills,
        public array $archetypes,
        public array $aspects,
        public int $power,
        public int $regard,
        public ?string $notes,
        public RelationLevel $level,
        public bool $faction = false,
        public ?string $id = null,
    ) {
        if (null === $id) {
            $this->id = (string)Str::uuid();
        }
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __get(string $name): mixed
    {
        if ('cost' === $name) {
            return $this->cost();
        }
        $trace = debug_backtrace();
        trigger_error(
            sprintf(
                'Undefined property via __get(): %s in %s on line %d',
                $name,
                // @phpstan-ignore-next-line
                $trace[0]['file'],
                // @phpstan-ignore-next-line
                $trace[0]['line'],
            ),
            E_USER_NOTICE
        );
        return null; // @codeCoverageIgnore
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @param array{
     *   name: string,
     *   skills: array<int, string>,
     *   archetypes: array<int, array{id: string, additional?: string}>,
     *   aspects: array<int, string>,
     *   level: string,
     *   increase_power: int,
     *   increase_regard: int,
     *   notes: ?string,
     *   faction: bool,
     *   id: string
     * } $relation
     */
    public static function fromArray(array $relation): self
    {
        $level = new RelationLevel($relation['level']);

        $archetypes = [];
        foreach ($relation['archetypes'] ?? [] as $archetype) {
            $archetypes[] = new RelationArchetype(
                $archetype['id'],
                $archetype['additional'] ?? null,
            );
        }

        $aspects = [];
        foreach ($relation['aspects'] ?? [] as $aspect) {
            $aspects[] = new RelationAspect($aspect);
        }

        $skills = [];
        foreach ($relation['skills'] ?? [] as $skill) {
            $skills[] = new Skill($skill);
        }

        return new Relation(
            name: $relation['name'],
            skills: $skills,
            archetypes: $archetypes,
            aspects: $aspects,
            power: $level->power + ($relation['increase_power'] ?? 0),
            regard: $level->regard + ($relation['increase_regard'] ?? 0),
            notes: (string)$relation['notes'],
            level: $level,
            faction: $relation['faction'],
            id: $relation['id'] ?? null,
        );
    }

    /**
     * @return array{
     *   name: string,
     *   skills: array<int, string>,
     *   archetypes: array<int, array{id: string, additional: ?string}>,
     *   aspects: array<int, string>,
     *   level: string,
     *   increase_power: int,
     *   increase_regard: int,
     *   notes: ?string,
     *   faction: bool,
     *   id: ?string
     * }
     */
    public function toArray(): array
    {
        $archetypes = [];
        foreach ($this->archetypes ?? [] as $archetype) {
            $archetypes[] = [
                'id' => $archetype->id,
                'additional' => '' !== $archetype->additional ? $archetype->additional : null,
            ];
        }

        $aspects = [];
        foreach ($this->aspects ?? [] as $aspect) {
            $aspects[] = $aspect->id;
        }

        $skills = [];
        foreach ($this->skills ?? [] as $skill) {
            $skills[] = $skill->id;
        }

        return [
            'name' => $this->name,
            'skills' => $skills,
            'archetypes' => $archetypes,
            'aspects' => $aspects,
            'level' => $this->level->id,
            'increase_power' => $this->power - $this->level->power,
            'increase_regard' => $this->regard - $this->level->regard,
            'notes' => $this->notes,
            'faction' => $this->faction,
            'id' => $this->id,
        ];
    }

    public function cost(): int
    {
        $cost = $this->level->cost;
        $cost += ($this->power - $this->level->power) * 5;
        $cost += ($this->regard - $this->level->regard) * 2;

        foreach ($this->aspects as $aspect) {
            switch ($aspect->id) {
                case 'adversarial':
                    $regardDifference = $this->level->regard - $this->regard;
                    $cost = max($cost - $regardDifference * 2, 0);
                    break;
                case 'dues':
                    $cost = ceil($cost / 2);
                    break;
                case 'multi-talented':
                    $extraSkills = count($this->skills) - 1;
                    $extraArchetypes = count($this->archetypes) - 1;
                    $cost = $cost + $cost * ($extraSkills + $extraArchetypes) / 2;
                    break;
                case 'supportive':
                    $cost += 15;
                    break;
                case 'toxic':
                    $cost = max($cost - 5, 1);
                    break;
            }
        }

        return (int)$cost;
    }
}
