<?php

declare(strict_types=1);

namespace App\Models\Avatar;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property Background $background
 * @property Era $era
 */
class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
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

    public function setBackgroundAttribute(Background $background): void
    {
        $this->attributes['background'] = $background->name;
    }

    public function setEraAttribute(Era $era): void
    {
        $this->attributes['era'] = $era->name;
    }
}
