<?php

declare(strict_types=1);

namespace App\Models\Transformers;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * @property int $endurance_alt
 * @property int $endurance_robot
 * @property-read int $energon_base
 * @property int $energon_current
 * @property-read int $hp_base
 * @property int $hp_current
 * @property-read string $id
 * @property int $intelligence_alt
 * @property int $intelligence_robot
 * @property-read Mode $mode
 * @property-write string|Mode $mode
 * @property-read Programming $programming
 * @property-write string|Programming $programming
 * @property string $quote
 * @property-read Size $size
 * @property-write int|Size $size
 * @property-read SubgroupArray $subgroups
 * @property-write array<int, string>|SubgroupArray $subgroups
 * @property-read WeaponArray $weapons
 * @property-write array<int, string>|WeaponArray $weapons
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'transformers',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'allegiance',
        'alt_mode',
        'color_primary',
        'color_secondary',
        'courage_alt',
        'courage_robot',
        'endurance_alt',
        'endurance_robot',
        'energon_current',
        'firepower_alt',
        'firepower_robot',
        'hp_current',
        'intelligence_alt',
        'intelligence_robot',
        'mode',
        'name',
        'owner',
        'programming',
        'quote',
        'rank_alt',
        'rank_robot',
        'size',
        'skill_alt',
        'skill_robot',
        'speed_alt',
        'speed_robot',
        'strength_alt',
        'strength_robot',
        'subgroups',
        'weapons',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed character';
    }

    /**
     * Force this model to only load for Transformers characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'transformers',
            function (Builder $builder): void {
                $builder->where('system', 'transformers');
            }
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function energonBase(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return 10 + (int)$this->intelligence_robot
                    + $this->size->energon();
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function energonCurrent(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->attributes['energon_current']
                    ?? $this->energon_base;
            },
            set: function (int $energon): int {
                return $energon;
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function hpBase(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return 10 + (int)$this->endurance_robot + $this->size->hp();
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function hpCurrent(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->attributes['hp_current'] ?? $this->hp_base;
            },
            set: function (int $hp): int {
                return $hp;
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function mode(): Attribute
    {
        return Attribute::make(
            get: function (?string $mode): Mode {
                return Mode::tryFrom(strtolower($mode ?? '')) ?? Mode::Robot;
            },
            set: function (string | Mode $mode): string {
                if ($mode instanceof Mode) {
                    return $mode->name;
                }
                return Mode::from(strtolower($mode))->name;
            },
        );
    }

    /**
     * The character's "function" has been renamed to "programming" since
     * "function" is a reserved word.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function programming(): Attribute
    {
        return Attribute::make(
            get: function (): ?Programming {
                return Programming::tryFrom(strtolower($this->attributes['programming'] ?? ''));
            },
            set: function (string | Programming $programming): string {
                if ($programming instanceof Programming) {
                    return $programming->name;
                }
                return Programming::from(strtolower($programming))->name;
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function size(): Attribute
    {
        return Attribute::make(
            get: function (?int $size): Size {
                return Size::from($size ?? 3);
            },
            set: function (int | Size $size): int {
                if ($size instanceof Size) {
                    return $size->value;
                }
                return Size::from($size)->value;
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function subgroups(): Attribute
    {
        return Attribute::make(
            get: function (): SubgroupArray {
                $groups = new SubgroupArray();
                foreach ($this->attributes['subgroups'] ?? [] as $group) {
                    try {
                        $groups[] = new Subgroup($group);
                    } catch (RuntimeException) {
                        Log::warning(
                            'Transformers character "{name}" ({id}) has '
                                . 'invalid subgroup {subgroup}',
                            [
                                'name' => $this->name,
                                'id' => $this->id,
                                'subgroup' => $group,
                            ]
                        );
                    }
                }
                return $groups;
            },
            set: function (array | SubgroupArray $groups): array {
                if ($groups instanceof SubgroupArray) {
                    $storableGroups = [];
                    foreach ($groups as $group) {
                        $storableGroups[] = $group->id;
                    }
                    $this->attributes['subgroups'] = $storableGroups;
                    return $storableGroups;
                }
                return ['subgroups' => $groups];
            },
        );
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function weapons(): Attribute
    {
        return Attribute::make(
            get: function (): WeaponArray {
                $weapons = new WeaponArray();
                foreach ($this->attributes['weapons'] ?? [] as $weapon) {
                    try {
                        $weapons[] = new Weapon($weapon);
                    } catch (RuntimeException) {
                        Log::warning(
                            'Transformers character "{name}" ({id}) has '
                                . 'invalid weapon {weapon}',
                            [
                                'name' => $this->name,
                                'id' => $this->id,
                                'subgroup' => $weapon,
                            ]
                        );
                    }
                }
                return $weapons;
            },
            set: function (array | WeaponArray $weapons): array {
                if ($weapons instanceof WeaponArray) {
                    $storableWeapons = [];
                    foreach ($weapons as $weapon) {
                        $storableWeapons[] = $weapon->id;
                    }
                    $this->attributes['weapons'] = $storableWeapons;
                    return $storableWeapons;
                }
                return ['weapons' => $weapons];
            },
        );
    }
}
