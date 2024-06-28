<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Blistercritters\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property int $instinct
 * @property string $name
 * @property int $noggin
 * @property int $scrap
 * @property int $scurry
 * @property-read int $starting_health
 * @property int $vibe
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<array-key, mixed>
     */
    protected $attributes = [
        'system' => 'blistercritters',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'instinct',
        'name',
        'noggin',
        'scrap',
        'scurry',
        'vibe',
    ];

    /**
     * @var array<array-key, string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Critter';
    }

    /**
     * Force this model to only load for Blister Critters characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'blistercritters',
            function (Builder $builder): void {
                $builder->where('system', 'blistercritters');
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
    public function startingHealth(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->scrap + $this->scurry;
            },
        );
    }
}
