<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Alien\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property ?string $agenda
 * @property int $agility
 * @property ?string $appearance
 * @property ?Armor $armor
 * @property string $buddy
 * @property string $career
 * @property int $cash
 * @property int $empathy
 * @property-read int $encumbrance
 * @property-read int $encumbrance_maximum
 * @property int $experience
 * @property-read int $health_maximum
 * @property-read array<int, Injury> $injuries
 * @property-write array<int, Injury|string> $injuries
 * @property string $name
 * @property int $radiation
 * @property string $rival
 * @property-read array<string, Skill> $skills
 * @property-write array<int|string, Skill|int> $skills
 * @property int $strength
 * @property int $stress
 * @property-read array<int, Talent> $talents
 * @property-write array<int, Talent|string> $talents
 * @property-read array<int, Weapon> $weapons
 * @property-write array<int, Weapon|string> $weapons
 * @property int $wits
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<array-key, mixed>
     */
    protected $attributes = [
        'system' => 'alien',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agenda',
        'agility',
        'appearance',
        'armor',
        'buddy',
        'career',
        'cash',
        'empathy',
        'experience',
        'health_current',
        'injuries',
        'items',
        'name',
        'radiation',
        'rival',
        'skills',
        'strength',
        'stress',
        'talents',
        'weapons',
        'wits',
    ];

    /**
     * @phpstan-ignore-next-line
     * @var array<array-key, string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed character';
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function agility(): Attribute
    {
        return Attribute::make(
            get: function (int $agility): int {
                foreach ($this->armor?->modifiers ?? [] as $modifier) {
                    if (Armor::MODIFIER_AGILITY_DECREASE === $modifier) {
                        $agility -= 1;
                    }
                }
                return $agility;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function armor(): Attribute
    {
        return Attribute::make(
            get: function (?string $armor): ?Armor {
                if (null === $armor) {
                    return null;
                }

                return new Armor($armor);
            },
            set: function (Armor|string $armor): string {
                if ($armor instanceof Armor) {
                    return $armor->id;
                }
                return $armor;
            },
        );
    }

    protected static function booted(): void
    {
        static::addGlobalScope(
            'alien',
            function (Builder $builder): void {
                $builder->where('system', 'alien');
            }
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function encumbrance(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $encumbrance = 0;
                if (null !== $this->armor?->weight) {
                    $encumbrance += $this->armor->weight;
                }
                foreach ($this->weapons ?? [] as $weapon) {
                    if (null !== $weapon->weight) {
                        $encumbrance += $weapon->weight;
                    }
                }
                return $encumbrance;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function encumbranceMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['strength'] * 2;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function healthMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['strength'];
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function injuries(): Attribute
    {
        return Attribute::make(
            get: function (?array $injuries): array {
                return array_map(
                    function (string $injury): Injury {
                        return new Injury($injury);
                    },
                    $injuries ?? [],
                );
            },
            set: function (array $injuries): array {
                if (current($injuries) instanceof Injury) {
                    return ['injuries' => array_map(
                        function (Injury $injury): string {
                            return $injury->id;
                        },
                        $injuries,
                    )];
                }
                return ['injuries' => $injuries];
            },
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function skills(): Attribute
    {
        return Attribute::make(
            get: function (?array $skills): array {
                $returnedSkills = collect(Skill::all())->keyBy('id');
                foreach ($skills ?? [] as $skill => $rank) {
                    // @phpstan-ignore property.nonObject
                    $returnedSkills[$skill]->rank = $rank;
                }
                foreach ($this->armor?->modifiers ?? [] as $modifier) {
                    switch ($modifier) {
                        case Armor::MODIFIER_CLOSE_COMBAT_INCREASE: // @codeCoverageIgnore
                            // @phpstan-ignore property.nonObject
                            $returnedSkills['close-combat']->rank += 3;
                            break;
                        case Armor::MODIFIER_HEAVY_MACHINERY_INCREASE: // @codeCoverageIgnore
                            // @phpstan-ignore property.nonObject
                            $returnedSkills['heavy-machinery']->rank += 3;
                            break;
                        case Armor::MODIFIER_SURVIVAL_INCREASE: // @codeCoverageIgnore
                            // @phpstan-ignore property.nonObject
                            $returnedSkills['survival']->rank += 3;
                            break;
                    }
                }
                foreach ($this->injuries as $injury) {
                    foreach ($injury->effects as $skill => $amount) {
                        if (!isset($returnedSkills[$skill])) {
                            continue;
                        }
                        $returnedSkills[$skill]->rank += $amount;
                    }
                }
                return $returnedSkills->toArray();
            },
            set: function (array $skills): array {
                if (current($skills) instanceof Skill) {
                    $rawSkills = [];
                    foreach ($skills as $skill) {
                        $rawSkills[$skill->id] = $skill->rank;
                    }
                    return ['skills' => $rawSkills];
                }
                return ['skills' => $skills];
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function talents(): Attribute
    {
        return Attribute::make(
            get: function (?array $talents): array {
                $returnedTalents = [];
                foreach ($talents ?? [] as $talentId) {
                    $returnedTalents[] = new Talent($talentId);
                }
                return $returnedTalents;
            },
            set: function (array $talents): array {
                if (current($talents) instanceof Talent) {
                    $rawTalents = [];
                    foreach ($talents as $talent) {
                        $rawTalents[] = $talent->id;
                    }
                    return ['talents' => $rawTalents];
                }
                return ['talents' => $talents];
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function weapons(): Attribute
    {
        return Attribute::make(
            get: function (?array $weapons): array {
                $returnedWeapons = [];
                foreach ($weapons ?? [] as $weaponId) {
                    $returnedWeapons[] = new Weapon($weaponId);
                }
                return $returnedWeapons;
            },
            set: function (array $weapons): array {
                if (current($weapons) instanceof Weapon) {
                    $rawWeapons = [];
                    foreach ($weapons as $weapon) {
                        $rawWeapons[] = $weapon->id;
                    }
                    return ['weapons' => $rawWeapons];
                }
                return ['weapons' => $weapons];
            },
        );
    }
}
