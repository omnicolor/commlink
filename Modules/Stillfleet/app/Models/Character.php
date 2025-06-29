<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\Services\DiceService;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use LogicException;
use Modules\Stillfleet\Database\Factories\CharacterFactory;
use Override;
use RuntimeException;
use Stringable;

use function array_walk;
use function assert;

/**
 * @property-read array<int, Power> $all_powers
 * @property string $charm
 * @property-read int $charm_modifier
 * @property string $combat
 * @property-read int $combat_modifier
 * @property int $grit
 * @property int $grit_current
 * @property int $health
 * @property int $health_current
 * @property string $movement
 * @property-read int $movement_modifier
 * @property string $name
 * @property Email $owner
 * @property int $rank
 * @property string $reason
 * @property-read int $reason_modifier
 * @property-read array<int, Role> $roles
 * @property-write array<int, array{id: string, level: int, powers: array<int, string>}> $roles
 * @property Species|null $species
 * @property string $will
 * @property-read int $will_modifier
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'stillfleet',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'combat' => 'string',
        'grit_current' => 'integer',
        'health_current' => 'integer',
        'money' => 'integer',
        'movement' => 'string',
        'owner' => AsEmail::class,
    ];

    /** @var list<string> */
    protected $fillable = [
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

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    public function allPowers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                // @phpstan-ignore nullsafe.neverNull
                $powers = $this->species?->powers ?? [];
                foreach ($this->roles ?? [] as $role) {
                    $powers = array_merge($powers, $role->powers);
                }
                return $powers;
            },
        );
    }

    /**
     * Force this model to only load for Stillfleet characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'stillfleet',
            function (Builder $builder): void {
                $builder->where('system', 'stillfleet');
            }
        );
    }

    public function charmModifier(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getPowersModifierForAttribute('CHA');
            },
        );
    }

    private function getPowersModifierForAttribute(string $attribute): int
    {
        $modifier = 0;
        foreach ($this->all_powers as $power) {
            foreach ($power->effects as $effect => $value) {
                if ($attribute === $effect) {
                    $modifier += $value;
                }
            }
        }
        return $modifier;
    }

    public function combatModifier(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getPowersModifierForAttribute('COM');
            },
        );
    }

    public function convert(): void
    {
        if ($this->health_current < 3) {
            throw new RuntimeException('Not enough health to convert');
        }
        $this->health_current -= 3;
        $this->grit_current += 1;
    }

    public function health(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return DiceService::rollMax($this->combat)
                    + DiceService::rollMax($this->movement);
            },
        );
    }

    public function healthCurrent(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->attributes['health_current'] ?? $this->health;
            },
            set: function (int $health): int {
                return $health;
            },
        );
    }

    public function grit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $grit = 0;
                if (isset($this->roles[0])) {
                    assert($this->roles[0] instanceof Role);
                    foreach ($this->roles[0]->grit as $attribute) {
                        if (Str::startsWith($attribute, '-')) {
                            $attribute = Str::after($attribute, '-');
                            $grit -= DiceService::rollMax($this->attributes[$attribute]);
                            continue;
                        }
                        $grit += DiceService::rollMax($this->attributes[$attribute]);
                    }
                }

                foreach ($this->all_powers as $power) {
                    foreach ($power->effects as $effect => $value) {
                        if ('GRT' === $effect) {
                            $grit += $value;
                        }
                    }
                }

                return $grit;
            },
            set: function (): never {
                throw new LogicException('Grit is a calculated attribute');
            },
        );
    }

    public function gritCurrent(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->attributes['grit_current'] ?? $this->grit;
            },
            set: function (int $grit): int {
                return $grit;
            },
        );
    }

    public function movementModifier(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getPowersModifierForAttribute('MOV');
            },
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function reasonModifier(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getPowersModifierForAttribute('REA');
            },
        );
    }

    public function roles(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $roles = [];
                foreach ($this->attributes['roles'] ?? [] as $role) {
                    /** @var Role */
                    $new_role = Role::findOrFail($role['id']);
                    $new_role->level = $role['level'];
                    $powers = $role['powers'] ?? [];
                    array_walk($powers, function (string &$power): void {
                        $power = Power::findOrFail($power);
                    });
                    $new_role->addPowers(...$powers);
                    $roles[] = $new_role;
                }
                return $roles;
            },
        );
    }

    public function species(): Attribute
    {
        return Attribute::make(
            get: function (): Species|null {
                try {
                    $species = Species::findOrFail($this->attributes['species'] ?? '');
                    assert($species instanceof Species);
                } catch (ModelNotFoundException) {
                    return null;
                }
                foreach ($this->attributes['species_powers'] ?? [] as $power) {
                    try {
                        $power = Power::findOrFail($power);
                        assert($power instanceof Power);
                        $species->addPowers($power);
                    } catch (ModelNotFoundException) {
                        // Ignore.
                    }
                }
                return $species;
            },
        );
    }

    public function willModifier(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->getPowersModifierForAttribute('WIL');
            },
        );
    }
}
