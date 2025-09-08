<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Dnd5e\Database\Factories\CharacterFactory;
use OutOfRangeException;
use Override;
use RuntimeException;
use Stringable;

use function floor;

/**
 * Representation of a D&D 5E character sheet.
 * @property int $charisma
 * @property int $constitution
 * @property int $dexterity
 * @property int $experience_points
 * @property int $intelligence
 * @property string $name
 * @property Email $owner
 * @property int $strength
 * @property string $system
 * @property int $wisdom
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    public const int ATTRIBUTE_MIN = 1;
    public const int ATTRIBUTE_MAX = 30;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'dnd5e',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
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
            function (Builder $builder): void {
                $builder->where('system', 'dnd5e');
            }
        );
    }

    /**
     * Return the ability modifier for the given attribute.
     * @throws OutOfRangeException If the attribute is < 1 or > 30
     * @throws RuntimeException If the attribute isn't set
     */
    public function getAbilityModifier(string $attribute): int
    {
        if (!isset($this->attributes[$attribute])) {
            throw new RuntimeException('Invalid attribute');
        }

        $value = $this->attributes[$attribute];
        if (self::ATTRIBUTE_MIN > $value || self::ATTRIBUTE_MAX < $value) {
            throw new OutOfRangeException('Attribute value is out of range');
        }

        return -5 + (int)floor($value / 2);
    }

    /**
     * Return the character's armor class.
     * @throws OutOfRangeException If the character's dexterity is invalid
     * @throws RuntimeException If the character's dexterity isn't set
     */
    public function getArmorClass(): int
    {
        return 10 + $this->getAbilityModifier('dexterity');
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }
}
