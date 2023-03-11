<?php

declare(strict_types=1);

namespace App\Models\Capers;

use App\Models\Character as BaseCharacter;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Representation of a Capers character.
 * @property int $advancementPoints
 * @property int $agility
 * @property string $background
 * @property-read string $body
 * @property int $charisma
 * @property string $description
 * @property int $expertise
 * @property ?Identity $identity
 * @property string $id
 * @property int $level
 * @property string $mannerisms
 * @property-read string $mind
 * @property int $moxie
 * @property string $name
 * @property int $perception
 * @property array<int, array<string, string>> $perks
 * @property PowerArray $powers
 * @property int $resilience
 * @property SkillArray $skills
 * @property-read int $speed
 * @property int $strength
 * @property string $type
 * @property ?Vice $vice
 * @property ?Virtue $virtue
 */
class Character extends BaseCharacter
{
    use HasFactory;

    public const TYPE_CAPER = 'caper';
    public const TYPE_EXCEPTIONAL = 'exceptional';
    public const TYPE_REGULAR = 'regular';

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'capers',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'advancement_points' => 'integer',
        'agility' => 'integer',
        'charisma' => 'integer',
        'expertise' => 'integer',
        'current_hits' => 'integer',
        'hits' => 'integer',
        'level' => 'integer',
        'moxie' => 'integer',
        'perception' => 'integer',
        'resilience' => 'integer',
        'strength' => 'integer',
    ];

    /**
     * The database connection that should be used by the model.
     * @var ?string
     */
    protected $connection = 'mongodb';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'agility',
        'background',
        'charisma',
        'description',
        'expertise',
        'gear',
        'hits',
        'current_hits',
        'identity',
        'level',
        'mannerisms',
        'money',
        'moxie',
        'name',
        'owner',
        'perception',
        'perks',
        'powers',
        'resilience',
        'skills',
        'strength',
        'type',
        'vice',
        'virtue',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        '_id',
    ];

    /**
     * Table to pull from.
     * @var string
     */
    protected $table = 'characters';

    /**
     * Return the character's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed Character';
    }

    /**
     * Force this model to only load for Capers characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'capers',
            function (Builder $builder): void {
                $builder->where('system', 'capers');
            }
        );
    }

    public function findAttributeAt(int $value): ?string
    {
        $key = array_search(
            $value,
            [
                'agility' => $this->attributes['agility'] ?? null,
                'charisma' => $this->attributes['charisma'] ?? null,
                'expertise' => $this->attributes['expertise'] ?? null,
                'perception' => $this->attributes['perception'] ?? null,
                'resilience' => $this->attributes['resilience'] ?? null,
                'strength' => $this->attributes['strength'] ?? null,
            ],
            true
        );
        if (false === $key) {
            return null;
        }
        return $key;
    }

    public function getBodyAttribute(): string
    {
        return $this->getTraitDefense('agility');
    }

    public function getGearAttribute(): GearArray
    {
        $gear = new GearArray();
        foreach ($this->attributes['gear'] ?? [] as $item) {
            try {
                $gear[] = Gear::get($item['id'], $item['quantity'] ?? 1);
            } catch (RuntimeException) {
                Log::warning(sprintf(
                    'Capers character "%s" has invalid gear "%s"',
                    (string)$this,
                    $item['id']
                ));
            }
        }
        return $gear;
    }

    public function getIdentityAttribute(): ?Identity
    {
        try {
            return new Identity($this->attributes['identity']);
        } catch (ErrorException) {
            return null;
        } catch (RuntimeException) {
            Log::warning(sprintf(
                'Caper character "%s" has invalid identity "%s"',
                (string)$this,
                $this->attributes['identity']
            ));
            return null;
        }
    }

    public function getMaximumHitsAttribute(): int
    {
        return 4 + (2 * (int)$this->resilience) + (2 * (int)$this->charisma);
    }

    public function getMindAttribute(): string
    {
        return $this->getTraitDefense('perception');
    }

    public function getPerks(): PerkArray
    {
        $perkArray = new PerkArray();
        foreach ($this->attributes['perks'] ?? [] as $rawPerk) {
            try {
                $perkArray[] = new Perk($rawPerk['id'], $rawPerk);
            } catch (RuntimeException $ex) {
                Log::warning(sprintf(
                    'Capers character "%s" has invalid perk "%s"',
                    (string)$this,
                    $rawPerk['id']
                ));
            }
        }
        return $perkArray;
    }

    public function getPowersAttribute(): PowerArray
    {
        $powers = new PowerArray();
        foreach ($this->attributes['powers'] ?? [] as $power) {
            try {
                $powers[$power['id']] = new Power(
                    $power['id'],
                    $power['rank'] ?? 1,
                    $power['boosts'] ?? []
                );
            } catch (RuntimeException) {
                Log::warning(sprintf(
                    'Capers character "%s" has invalid power "%s"',
                    (string)$this,
                    $power['id']
                ));
            }
        }
        return $powers;
    }

    public function getSkillsAttribute(): SkillArray
    {
        $skills = new SkillArray();
        foreach ($this->attributes['skills'] ?? [] as $skillId) {
            try {
                $skills[$skillId] = new Skill($skillId);
            } catch (RuntimeException) {
                Log::warning(\sprintf(
                    'Capers character "%s" has invalid skill "%s"',
                    (string)$this,
                    $skillId
                ));
            }
        }
        return $skills;
    }

    public function getSpeedAttribute(): int
    {
        foreach ($this->getPerks() as $perk) {
            if ('fleet-of-foot' === $perk->id) {
                return 40;
            }
        }
        return 30;
    }

    public function getStrengthAttribute(): int
    {
        $strength = $this->attributes['strength'] ?? 0;
        foreach ($this->powers as $power) {
            if ('super-strength' !== $power->id) {
                continue;
            }
            $strength += $power->rank;
        }
        return $strength;
    }

    /**
     * Return the necessary card to overcome the character's raw ability in the
     * given trait.
     * @param string $trait
     * @return string
     */
    public function getTraitDefense(string $trait): string
    {
        if (!isset($this->attributes[$trait])) {
            return '?';
        }

        switch ((int)$this->attributes[$trait]) {
            case 1:
                return '8';
            case 2:
                return '9';
            case 3:
                return '10';
            case 4:
                return 'J';
            case 5:
                return 'Q';
            default:
                throw new RuntimeException(sprintf(
                    'Invalid trait value for trait %s: %d',
                    $trait,
                    $this->attributes[$trait]
                ));
        }
    }

    public function getViceAttribute(): ?Vice
    {
        try {
            return new Vice($this->attributes['vice'] ?? '');
        } catch (RuntimeException) {
            Log::warning(sprintf(
                'Caper character "%s" has invalid vice "%s"',
                (string)$this,
                $this->attributes['vice'] ?? ''
            ));
            return null;
        }
    }

    public function getVirtueAttribute(): ?Virtue
    {
        try {
            return new Virtue($this->attributes['virtue'] ?? '');
        } catch (RuntimeException) {
            Log::warning(sprintf(
                'Caper character "%s" has invalid virtue "%s"',
                (string)$this,
                $this->attributes['virtue'] ?? ''
            ));
            return null;
        }
    }
}
