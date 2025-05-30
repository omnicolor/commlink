<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Blistercritters\Database\Factories\CharacterFactory;
use Override;
use Stringable;

/**
 * A critter's attributes (scrap, scurry, noggin, instinct, and vibe) are stored
 * as an integer which represents the die type to use for those rolls.
 * @method static ?self findOrFail(string $id)
 * @property string $created_at
 * @property string $id
 * @property int $instinct
 * @property string $name
 * @property int $noggin
 * @property int $scrap
 * @property int $scurry
 * @property-read int $starting_health
 * @property string $system
 * @property string $updated_at
 * @property int $vibe
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'blistercritters',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'instinct',
        'name',
        'noggin',
        'scrap',
        'scurry',
        'vibe',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Critter';
    }

    /**
     * Force this model to only load for Blister Critters characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'blistercritters',
            function (Builder $builder): void {
                $builder->where('system', 'blistercritters');
            }
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
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
