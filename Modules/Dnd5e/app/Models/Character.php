<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Dnd5e\Casts\AsAbilityValue;
use Modules\Dnd5e\Database\Factories\CharacterFactory;
use Modules\Dnd5e\ValueObjects\AbilityValue;
use Modules\Dnd5e\ValueObjects\CharacterLevel;
use Override;
use Stringable;

/**
 * Representation of a D&D 5E character sheet.
 * @property-read int $armor_class
 * @property AbilityValue $charisma
 * @property AbilityValue $constitution
 * @property AbilityValue $dexterity
 * @property int $experience_points
 * @property AbilityValue $intelligence
 * @property-read CharacterLevel $level
 * @property string $name
 * @property Email $owner
 * @property AbilityValue $strength
 * @property string $system
 * @property AbilityValue $wisdom
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'dnd5e',
    ];

    /** @var array<string, class-string> */
    protected $casts = [
        'charisma' => AsAbilityValue::class,
        'constitution' => AsAbilityValue::class,
        'dexterity' => AsAbilityValue::class,
        'intelligence' => AsAbilityValue::class,
        'strength' => AsAbilityValue::class,
        'wisdom' => AsAbilityValue::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'alignment',
        'charisma',
        'classes',
        'constitution',
        'dexterity',
        'equipment',
        'experience_points',
        'languages',
        'hitDice',
        'hitDieType',
        'hitPoints',
        'hitPointsCurrent',
        'intelligence',
        'money',
        'name',
        'race',
        'strength',
        'wisdom',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for D&D 5E characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'dnd5e',
            static function (Builder $builder): void {
                $builder->where('system', 'dnd5e');
            }
        );
    }

    /**
     * Return the character's armor class.
     */
    protected function armorClass(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return 10 + $this->dexterity->modifier;
            },
        );
    }

    protected function level(): Attribute
    {
        return Attribute::make(
            get: function (): CharacterLevel {
                return new CharacterLevel($this->attributes['experience_points'] ?? 0);
            },
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
