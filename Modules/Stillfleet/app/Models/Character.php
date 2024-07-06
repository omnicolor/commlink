<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use App\Models\Character as BaseCharacter;
use App\Services\DiceService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use LogicException;
use Modules\Stillfleet\Database\Factories\CharacterFactory;
use RuntimeException;

/**
 * @property string $charm
 * @property string $combat
 * @property int $grit
 * @property int $grit_current
 * @property int $health
 * @property int $health_current
 * @property string $movement
 * @property string $name
 * @property int $rank
 * @property string $reason
 * @property array $roles
 * @property string $species
 * @property string $will
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'stillfleet',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'grit_current' => 'integer',
        'health_current' => 'integer',
        'money' => 'integer',
    ];

    /**
     * @var array<int, string>
     */
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
        'species-power',
        'teloi',
        'will',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * @var string
     */
    protected $table = 'characters';

    /**
     * Force this model to only load for Stillfleet characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'stillfleet',
            function (Builder $builder): void {
                $builder->where('system', 'stillfleet');
            }
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function convert(): void
    {
        if ($this->health_current < 3) {
            throw new RuntimeException('Not enough health to convert');
        }
        $this->health_current = $this->health_current - 3;
        $this->grit_current = $this->grit_current + 1;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function health(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return DiceService::rollMax($this->combat)
                    + DiceService::rollMax($this->movement);
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function grit(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $grit = 0;
                foreach ($this->roles[0]->grit as $attribute) {
                    if (Str::startsWith($attribute, '-')) {
                        $attribute = Str::after($attribute, '-');
                        $grit -= DiceService::rollMax($this->attributes[$attribute]);
                        continue;
                    }
                    $grit += DiceService::rollMax($this->attributes[$attribute]);
                }
                return $grit;
            },
            set: function (): never {
                throw new LogicException('Grit is a calculated attribute');
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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

    protected static function newFactory(): Factory
    {
        // @phpstan-ignore-next-line
        return CharacterFactory::new();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function roles(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $roles = [];
                foreach ($this->attributes['roles'] ?? [] as $role) {
                    $roles[] = new Role(
                        $role['id'],
                        $role['level'],
                        $role['powers'] ?? [],
                    );
                }
                return $roles;
            },
        );
    }
}
