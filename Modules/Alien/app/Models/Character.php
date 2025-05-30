<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Alien\Database\Factories\CharacterFactory;
use Override;
use Stringable;

use function array_map;
use function assert;
use function collect;
use function current;

/**
 * @property ?string $agenda
 * @property int $agility
 * @property int $agility_unmodified
 * @property ?string $appearance
 * @property ?Armor $armor
 * @property string $buddy
 * @property-read Career $career
 * @property-write Career|string $career
 * @property int $cash
 * @property int $empathy
 * @property-read int $encumbrance
 * @property-read int $encumbrance_maximum
 * @property int $experience
 * @property-read array<int, Gear> $gear
 * @property-write array<int, Gear|array<string, int|string>> $gear
 * @property int $health
 * @property int $health_current
 * @property-read int $health_maximum
 * @property-read array<int, Injury> $injuries
 * @property-write array<int, Injury|string> $injuries
 * @property Email $owner
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

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'alien',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
    ];

    /** @var list<string> */
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
        'gear',
        'health_current',
        'injuries',
        'items',
        'name',
        'owner',
        'radiation',
        'rival',
        'skills',
        'strength',
        'stress',
        'talents',
        'weapons',
        'wits',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed character';
    }

    protected function agility(): Attribute
    {
        return Attribute::make(
            get: function (int|null $agility): int|null {
                if (null === $agility) {
                    return null;
                }
                foreach ($this->armor->modifiers ?? [] as $modifier) {
                    if (Armor::MODIFIER_AGILITY_DECREASE === $modifier) {
                        $agility -= 1;
                    }
                }
                return $agility;
            },
        );
    }

    protected function agilityUnmodified(): Attribute
    {
        return Attribute::make(
            get: function (): int|null {
                return $this->attributes['agility'] ?? null;
            },
        );
    }

    protected function armor(): Attribute
    {
        return Attribute::make(
            get: function (?string $armor): ?Armor {
                if (null === $armor) {
                    return null;
                }

                return new Armor($armor);
            },
            set: function (Armor|null|string $armor): ?string {
                if ($armor instanceof Armor) {
                    return $armor->id;
                }
                return $armor;
            },
        );
    }

    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'alien',
            function (Builder $builder): void {
                $builder->where('system', 'alien');
            }
        );
    }

    protected function career(): Attribute
    {
        return Attribute::make(
            get: function (?string $career): ?Career {
                if (null === $career) {
                    return null;
                }

                return new Career($career);
            },
            set: function (Career|string $career): string {
                if ($career instanceof Career) {
                    return $career->id;
                }
                return $career;
            },
        );
    }

    protected function cash(): Attribute
    {
        return Attribute::make(
            get: function (?int $cash): int {
                return $cash ?? 0;
            },
        );
    }

    protected function encumbrance(): Attribute
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

    protected function encumbranceMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['strength'] * 2;
            },
        );
    }

    protected function gear(): Attribute
    {
        return Attribute::make(
            get: function (array|null $gear): array {
                return array_map(
                    function (array $item): Gear {
                        return new Gear($item['id'], $item['quantity'] ?? null);
                    },
                    $gear ?? [],
                );
            },
            set: function (array $gear): array {
                if (current($gear) instanceof Gear) {
                    return ['gear' => array_map(
                        function (Gear $item): array {
                            return [
                                'id' => $item->id,
                                'quantity' => $item->quantity,
                            ];
                        },
                        $gear,
                    )];
                }
                return ['gear' => $gear];
            },
        );
    }

    protected function healthCurrent(): Attribute
    {
        return Attribute::make(
            get: function (int|null $health_current): int {
                return $health_current ?? $this->health_maximum;
            },
        );
    }

    protected function healthMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)($this->attributes['strength'] ?? 0);
            },
        );
    }

    protected function injuries(): Attribute
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

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    protected function radiation(): Attribute
    {
        return Attribute::make(
            get: function (int|null $radiation): int {
                return $radiation ?? 0;
            },
        );
    }

    protected function skills(): Attribute
    {
        return Attribute::make(
            get: function (?array $skills): array {
                $returnedSkills = collect(Skill::all())->keyBy('id');
                foreach ($skills ?? [] as $skill => $rank) {
                    assert($returnedSkills[$skill] instanceof Skill);
                    $returnedSkills[$skill]->rank = $rank;
                }
                foreach ($this->armor->modifiers ?? [] as $modifier) {
                    switch ($modifier) {
                        case Armor::MODIFIER_CLOSE_COMBAT_INCREASE: // @codeCoverageIgnore
                            assert($returnedSkills['close-combat'] instanceof Skill);
                            $returnedSkills['close-combat']->rank += 3;
                            break;
                        case Armor::MODIFIER_HEAVY_MACHINERY_INCREASE: // @codeCoverageIgnore
                            assert($returnedSkills['heavy-machinery'] instanceof Skill);
                            $returnedSkills['heavy-machinery']->rank += 3;
                            break;
                        case Armor::MODIFIER_SURVIVAL_INCREASE: // @codeCoverageIgnore
                            assert($returnedSkills['survival'] instanceof Skill);
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

    protected function talents(): Attribute
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

    protected function weapons(): Attribute
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
