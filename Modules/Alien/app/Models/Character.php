<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Alien\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property ?string $agenda
 * @property int $agility
 * @property ?string $appearance
 * @property string $buddy
 * @property string $career
 * @property int $cash
 * @property int $empathy
 * @property-read int $encumbrance
 * @property-read int $encumbrance_maximum
 * @property int $experience
 * @property-read int $health_maximum
 * @property string $name
 * @property string $rival
 * @property int $strength
 * @property int $stress
 * @property int $wits
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<array-key, mixed>
     */
    protected $attributes = [
        'system' => 'alien',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agenda',
        'agility',
        'appearance',
        'buddy',
        'career',
        'cash',
        'empathy',
        'experience',
        'health_current',
        'items',
        'name',
        'rival',
        'strength',
        'stress',
        'wits',
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
        return $this->name ?? 'Unnamed character';
    }

    protected static function booted(): void
    {
        static::addGlobalScope(
            'alien',
            function (Builder $builder): void {
                $builder->where('system', 'alien');
            }
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function encumbrance(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return 0;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function encumbranceMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['strength'] * 2;
            },
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function healthMaximum(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return (int)$this->attributes['strength'];
            },
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
