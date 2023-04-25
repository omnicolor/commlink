<?php

declare(strict_types=1);

namespace App\Models\Transformers;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read Programming $programming
 * @property-write string|Programming $programming
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
        'altMode',
        'colorPrimary',
        'colorSecondary',
        'courageAlt',
        'courageRobot',
        'enduranceAlt',
        'enduranceRobot',
        'firepowerAlt',
        'firepowerRobot',
        'name',
        'intelligenceAlt',
        'intelligenceRobot',
        'programming',
        'rank',
        'skillAlt',
        'skillRobot',
        'speedAlt',
        'speedRobot',
        'strengthAlt',
        'strengthRobot',
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
        return $this->attributes['name'] ?? 'Unnamed character';
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
    public function programming(): Attribute
    {
        return Attribute::make(
            get: function (array $attributes): Programming {
                return Programming::from($attributes['programming']);
            },
            set: function (string | Programming $programming): string {
                if ($programming instanceof Programming) {
                    return $programming->name;
                }
                Programming::from($programming);
                return $programming;
            },
        );
    }
}
