<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;
use Modules\Capers\Database\Factories\CharacterFactory;
use Override;
use RuntimeException;
use Stringable;

use function array_search;
use function sprintf;

/**
 * Representation of a Capers character.
 * @property int $advancementPoints
 * @property int $agility
 * @property string $background
 * @property-read string $body
 * @property int $charisma
 * @property string $description
 * @property int $expertise
 * @property-read GearArray $gear
 * @property-write array<int, array<string, mixed>> $gear
 * @property ?Identity $identity
 * @property string $id
 * @property int $level
 * @property string $mannerisms
 * @property-read string $mind
 * @property int $moxie
 * @property string $name
 * @property int $perception
 * @property array<int, array<string, string>> $perks
 * @property-read PowerArray $powers
 * @property-write array<int|string, mixed>|PowerArray $powers
 * @property int $resilience
 * @property SkillArray $skills
 * @property-read int $speed
 * @property int $strength
 * @property string $type
 * @property ?Vice $vice
 * @property ?Virtue $virtue
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    public const string TYPE_CAPER = 'caper';
    public const string TYPE_EXCEPTIONAL = 'exceptional';
    public const string TYPE_REGULAR = 'regular';

    protected const int SPEED_DEFAULT = 30;
    protected const int SPEED_FLEET_OF_FOOT = 40;

    /** @var array<string, mixed> */
    protected $attributes = [
        'powers' => [],
        'system' => 'capers',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'advancement_points' => 'integer',
        'agility' => 'integer',
        'charisma' => 'integer',
        'expertise' => 'integer',
        'current_hits' => 'integer',
        'hits' => 'integer',
        'level' => 'integer',
        'moxie' => 'integer',
        'owner' => AsEmail::class,
        'perception' => 'integer',
        'resilience' => 'integer',
        'strength' => 'integer',
    ];

    /** @var list<string> */
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
     * Force this model to only load for Capers characters.
     */
    #[Override]
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
                Log::warning(
                    'Capers character "{name}" has invalid gear "{gear}"',
                    [
                        'name' => (string)$this,
                        'gear' => $item['id'],
                    ]
                );
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
            Log::warning(
                'Capers character "{name}" has invalid identity "{identity}"',
                [
                    'name' => (string)$this,
                    'identity' => $this->attributes['identity'],
                ]
            );
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
            } catch (RuntimeException) {
                Log::warning(
                    'Capers character "{name}" has invalid perk "{perk}"',
                    [
                        'name' => (string)$this,
                        'perk' => $rawPerk['id'],
                    ]
                );
            }
        }
        return $perkArray;
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function powers(): Attribute
    {
        return Attribute::make(
            get: function (): PowerArray {
                $powers = new PowerArray();
                foreach ($this->attributes['powers'] ?? [] as $power) {
                    try {
                        $powers[$power['id']] = new Power(
                            $power['id'],
                            $power['rank'] ?? 1,
                            $power['boosts'] ?? []
                        );
                    } catch (RuntimeException) {
                        Log::warning(
                            'Capers character "{name}" has invalid power "{power}"',
                            [
                                'name' => (string)$this,
                                'power' => $power['id'],
                            ]
                        );
                    }
                }
                return $powers;
            },
            set: function (array|PowerArray $powers): array {
                if ($powers instanceof PowerArray) {
                    $textPowers = [];
                    foreach ($powers as $power) {
                        $boosts = [];
                        foreach ($power->boosts as $boost) {
                            $boosts[] = $boost->id;
                        }
                        $textPowers[] = [
                            'id' => $power->id,
                            'rank' => $power->rank,
                            'boosts' => $boosts,
                        ];
                    }
                    $powers = $textPowers;
                }
                return ['powers' => $powers];
            },
        );
    }

    public function getSkillsAttribute(): SkillArray
    {
        $skills = new SkillArray();
        foreach ($this->attributes['skills'] ?? [] as $skillId) {
            try {
                $skills[$skillId] = new Skill($skillId);
            } catch (RuntimeException) {
                Log::warning(
                    'Capers character "{name}" has invalid skill "{skill}"',
                    [
                        'name' => (string)$this,
                        'skill' => $skillId,
                    ]
                );
            }
        }
        return $skills;
    }

    public function getSpeedAttribute(): int
    {
        foreach ($this->getPerks() as $perk) {
            if ('fleet-of-foot' === $perk->id) {
                return self::SPEED_FLEET_OF_FOOT;
            }
        }
        return self::SPEED_DEFAULT;
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
     */
    public function getTraitDefense(string $trait): string
    {
        if (!isset($this->attributes[$trait])) {
            return '?';
        }

        return match ((int)$this->attributes[$trait]) {
            1 => '8',
            2 => '9',
            3 => '10',
            4 => 'J',
            5 => 'Q',
            default => throw new RuntimeException(sprintf(
                'Invalid trait value for trait %s: %d',
                $trait,
                $this->attributes[$trait]
            )),
        };
    }

    public function getViceAttribute(): ?Vice
    {
        try {
            return new Vice($this->attributes['vice'] ?? '');
        } catch (RuntimeException) {
            Log::warning(
                'Capers character "{name}" has invalid vice "{vice}"',
                [
                    'name' => (string)$this,
                    'vice' => $this->attributes['vice'] ?? '',
                ]
            );
            return null;
        }
    }

    public function getVirtueAttribute(): ?Virtue
    {
        try {
            return new Virtue($this->attributes['virtue'] ?? '');
        } catch (RuntimeException) {
            Log::warning(
                'Capers character "{name}" has invalid virtue "{virtue}"',
                [
                    'name' => (string)$this,
                    'virtue' => $this->attributes['virtue'] ?? '',
                ]
            );
            return null;
        }
    }
}
