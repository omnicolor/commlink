<?php

declare(strict_types=1);

namespace App\Models\Dnd5e;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Representation of a D&D 5E character sheet.
 * @property int $charisma
 * @property int $constitution
 * @property int $dexterity
 * @property int $intelligence
 * @property string $owner
 * @property int $strength
 * @property string $system
 * @property int $wisdom
 */
class Character extends \App\Models\Character
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'dnd5e',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'alignment',
        'charisma',
        'classes',
        'constitution',
        'dexterity',
        'equipment',
        'experience',
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
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for D&D 5E characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'dnd5e',
            function (Builder $builder): void {
                $builder->where('system', 'dnd5e');
            }
        );
    }

    /**
     * Return the ability modifier for the given attribute.
     * @param string $attribute Attribute to return modifier for
     * @return int
     * @throws \RuntimeException If the attribute isn't set
     * @throws \OutOfRangeException If the attribute is < 1 or > 30
     */
    public function getAbilityModifier(string $attribute): int
    {
        if (!isset($this->attributes[$attribute])) {
            throw new \RuntimeException('Invalid attribute');
        }

        $value = $this->attributes[$attribute];
        if (1 > $value || 30 < $value) {
            throw new \OutOfRangeException('Attribute value is out of range');
        }

        return -5 + (int)floor($value / 2);
    }

    /**
     * Return the character's armor class.
     * @return int
     * @throws \RuntimeException If the character's dexterity isn't set
     * @throws \OutOfRangeException If the character's dexterity is invalid
     */
    public function getArmorClass(): int
    {
        return 10 + $this->getAbilityModifier('dexterity');
    }
}
