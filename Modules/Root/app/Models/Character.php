<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Root\Casts\AttributeCast;
use Modules\Root\Database\Factories\CharacterFactory;
use Modules\Root\ValueObjects\Attribute;
use Stringable;

/**
 * @property Attribute $charm
 * @property Attribute $cunning
 * @property Attribute $finese
 * @property string $look
 * @property Attribute $luck
 * @property Attribute $might
 * @property string $name
 * @property string $species
 * @property string $system
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<array-key, mixed>
     */
    protected $attributes = [
        'system' => 'root',
    ];

    protected $casts = [
        'charm' => AttributeCast::class,
        'cunning' => AttributeCast::class,
        'finese' => AttributeCast::class,
        'luck' => AttributeCast::class,
        'might' => AttributeCast::class,
        'name' => 'string',
        'system' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'charm',
        'cunning',
        'finese',
        'luck',
        'might',
        'name',
        'species',
        'system',
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

    protected static function booted(): void
    {
        static::addGlobalScope(
            'root',
            function (Builder $builder): void {
                $builder->where('system', 'root');
            }
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
