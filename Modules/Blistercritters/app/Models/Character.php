<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stringable;

/**
 * @property-read int $starting_health
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<string, mixed>
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
     * @var array<int, string>
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

    public function startingHealth(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->scrap + $this->scurry;
            },
        );
    }
}
