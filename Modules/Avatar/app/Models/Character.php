<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Avatar\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property string $appearance
 * @property-read Background $background
 * @property-write Background|string $background
 * @property string $creativity
 * @property-read Era $era
 * @property-write Era|string $era
 * @property string $fatigue
 * @property string $focus
 * @property string $harmony
 * @property string $history
 * @property string $name
 * @property string $passion
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<array-key, mixed>
     */
    protected $attributes = [
        'system' => 'avatar',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'appearance',
        'background',
        //'balance',
        //'conditions',
        'creativity',
        'era',
        'fatigue',
        'focus',
        'harmony',
        'history',
        'name',
        'passion',
        //'playbook',
        //'statuses',
        //'techniques',
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
        return (string)($this->attributes['name'] ?? 'Unnamed character');
    }

    /**
     * Force this model to only load for Avatar characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'avatar',
            function (Builder $builder): void {
                $builder->where('system', 'avatar');
            }
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function background(): Attribute
    {
        return Attribute::make(
            get: function (): Background {
                return Background::from($this->attributes['background']);
            },
            set: function (string | Background $background): string {
                if ($background instanceof Background) {
                    return $background->value;
                }
                return Background::from($background)->value;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function era(): Attribute
    {
        return Attribute::make(
            get: function (): Era {
                return Era::from($this->attributes['era']);
            },
            set: function (string | Era $era): string {
                if ($era instanceof Era) {
                    return $era->value;
                }
                return Era::from($era)->value;
            },
        );
    }
}
