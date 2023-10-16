<?php

declare(strict_types=1);

namespace App\Models\Stillfleet;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use LogicException;
use RuntimeException;

/**
 * @property int $charm
 * @property int $combat
 * @property-read int $grit
 * @property int $grit_current
 * @property int $health
 * @property int $health_current
 * @property int $movement
 * @property string $name
 * @property int $rank
 * @property int $reason
 * @property array $roles
 * @property string $species
 * @property array $will
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'charm' => 'integer',
        'combat' => 'integer',
        'grit_current' => 'integer',
        'health_current' => 'integer',
        'movement' => 'integer',
        'reason' => 'integer',
        'will' => 'integer',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'charm',
        'combat',
        'grit',
        'grit_current',
        'health',
        'health_current',
        'movement',
        'name',
        'rank',
        'reason',
        'roles',
        'species',
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

    public function convert(): void
    {
        if ($this->health_current < 3) {
            throw new RuntimeException('Not enough health to convert');
        }
        $this->health_current = $this->health_current - 3;
        $this->grit_current = $this->grit_current + 1;
    }

    public function health(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->combat + $this->movement;
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
                $role = $this->roles[0];
                $attributes = $role->grit;
                return $this->attributes[$attributes[0]]
                    + $this->attributes[$attributes[1]];
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

    public function roles(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $roles = [];
                foreach ($this->attributes['roles'] as $role) {
                    $roles[] = new Role($role['id'], $role['level']);
                }
                return $roles;
            },
        );
    }
}
