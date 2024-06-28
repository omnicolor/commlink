<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Avatar\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property Background $background
 * @property Era $era
 * @psalm-suppress UndefinedClass
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
    public function setBackgroundAttribute(Background $background): void
    {
        $this->attributes['background'] = $background->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setEraAttribute(Era $era): void
    {
        $this->attributes['era'] = $era->name;
    }
}
