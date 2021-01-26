<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

use Illuminate\Database\Eloquent\Builder;

/**
 * Representation of a Shadowrun 5E character.
 * @property string $handle
 */
class Character extends \App\Models\Character
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'shadowrun5e',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'handle',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * Return the character's handle.
     * @return string
     */
    public function __toString(): string
    {
        return $this->handle;
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'shadowrun5e',
            function (Builder $builder): void {
                $builder->where('type', 'shadowrun5e');
            }
        );
    }
}
