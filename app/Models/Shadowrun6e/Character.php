<?php

declare(strict_types=1);

namespace App\Models\Shadowrun6e;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class Character extends BaseCharacter
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'shadowrun6e',
    ];

    /**
     * Attributes that need to be cast to a type.
     * @var array<string, string>
     */
    protected $casts = [
        'agility' => 'integer',
        'body' => 'integer',
        'charisma' => 'integer',
        'edge' => 'integer',
        'intuition' => 'integer',
        'karma' => 'integer',
        'karma_total' => 'integer',
        'logic' => 'integer',
        'nuyen' => 'integer',
        'reaction' => 'integer',
        'strength' => 'integer',
        'willpower' => 'integer',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agility',
        'armor',
        'augmentations',
        'body',
        'charisma',
        'complex_forms',
        'contacts',
        'edge',
        'gear',
        'handle',
        'identities',
        'intuition',
        'karma',
        'karma_total',
        'logic',
        'magic',
        'name',
        'nuyen',
        'powers',
        'qualities',
        'reaction',
        'resonance',
        'skills',
        'spells',
        'strength',
        'vehicles',
        'weapons',
        'willpower',
    ];

    /**
     * @var array<int, string>
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
        return $this->handle ?? $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Shadowrun 6E characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'shadowrun6e',
            function (Builder $builder): void {
                $builder->where('system', 'shadowrun6e');
            }
        );
    }

    /**
     * Return a collection of the character's qualities.
     * @return array<int, Quality>
     */
    public function getQualities(): array
    {
        $qualities = [];
        foreach ($this->qualities ?? [] as $quality) {
            try {
                $qualities[] = new Quality($quality['id']);
            } catch (RuntimeException) {
                Log::warning(\sprintf(
                    'Shadowrun6E character "%s" (%s) has invalid quality ID "%s"',
                    $this->handle,
                    $this->id,
                    $quality['id']
                ));
            }
        }
        return $qualities;
    }

    public function initiativeBase(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->intuition + $this->reaction;
            },
        );
    }

    public function initiativeDice(): Attribute
    {
        return Attribute::make(
            get: function () {
                return 1;
            },
        );
    }
}
