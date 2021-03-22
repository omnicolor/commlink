<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

use Illuminate\Database\Eloquent\Builder;

/**
 * Representation of a Cyberpunk Red character sheet.
 * @property int $body
 * @property int $cool
 * @property int $dexterity
 * @property int $empathy
 * @property string $handle
 * @property int $hitPointsCurrent
 * @property int $hitPointsMax
 * @property int $intelligence
 * @property int $luck
 * @property int $movement
 * @property int $reflexes
 * @property int $technique
 * @property int $willpower
 */
class Character extends \App\Models\Character
{
    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'cyberpunkred',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'body',
        'cool',
        'dexterity',
        'empathy',
        'handle',
        'hitPointsCurrent',
        'hitPointsMax',
        'intelligence',
        'luck',
        'movement',
        'reflexes',
        'technique',
        'willpower',
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * Return the character's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->handle;
    }

    /**
     * Force this model to only load for Cyberpunk Red characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'cyberpunkred',
            function (Builder $builder): void {
                $builder->where('system', 'cyberpunkred');
            }
        );
    }
}
