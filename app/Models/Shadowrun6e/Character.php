<?php

declare(strict_types=1);

namespace App\Models\Shadowrun6e;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Representation of a Shadowrun 6E character.
 * @property int $agility
 * @property array $armor
 * @property array $augmentations
 * @property int $body
 * @property int $charisma
 * @property array<int, mixed> $complex_forms
 * @property array<int, mixed> $contacts
 * @property int $edge
 * @property array<int, mixed> $gear
 * @property ?string $handle
 * @property-read string $id
 * @property array<int, mixed> $identities
 * @property-read int $initiative_base
 * @property-read int $initiative_dice
 * @property int $intuition
 * @property int $karma
 * @property int $karma_total
 * @property int $logic
 * @property ?int $magic
 * @property ?string $name
 * @property int $nuyen
 * @property array<int, mixed> $powers
 * @property array<int, array<string, int|string>> $qualities
 * @property int $reaction
 * @property ?int $resonance
 * @property array<int, mixed> $skills
 * @property array<int, mixed> $spells
 * @property int $strength
 * @property array<int, mixed> $vehicles
 * @property array<int, mixed> $weapons
 * @property int $willpower
 */
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
                $qualities[] = new Quality((string)$quality['id']);
            } catch (RuntimeException) {
                Log::warning(
                    'Shadowrun 6E character "{name}" ({id}) has invalid quality ID "{quality}"',
                    [
                        'name' => $this->handle,
                        'id' => $this->id,
                        'quality' => $quality['id'],
                    ]
                );
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
