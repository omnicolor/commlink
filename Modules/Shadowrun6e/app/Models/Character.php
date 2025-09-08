<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Modules\Shadowrun6e\Database\Factories\CharacterFactory;
use Modules\Shadowrun6e\ValueObjects\Attribute;
use Override;
use Stringable;

/**
 * Representation of a Shadowrun 6E character.
 * @property array<int, ActiveSkill> $active_skills
 * @property-read Attribute $agility
 * @property array<int, string> $armor
 * @property array<int, string> $augmentations
 * @property-read Attribute $body
 * @property-read Attribute $charisma
 * @property-read int $composure
 * @property array<int, mixed> $contacts
 * @property int|null $drain_dice
 * @property-read Attribute $edge
 * @property-read Attribute $edge_current
 * @property array<int, mixed> $gear
 * @property ?string $handle
 * @property-read string $id
 * @property array<int, mixed> $identities
 * @property-read int $initiative_base
 * @property-read int $initiative_dice
 * @property-read Attribute $intuition
 * @property-read int $judge_intentions
 * @property int $karma
 * @property int $karma_total
 * @property-read int $lift
 * @property-read Attribute $logic
 * @property-read Attribute|null $magic
 * @property-read int $memory
 * @property ?string $name
 * @property int $nuyen
 * @property Email $owner
 * @property array<int, Quality> $qualities
 * @property-read Attribute $reaction
 * @property-read Attribute|null $resonance
 * @property-read Attribute $strength
 * @property-read int $surprise_dice
 * @property-read Tradition|null $tradition
 * @property array<int, mixed> $vehicles
 * @property array<int, mixed> $weapons
 * @property Attribute $willpower
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'shadowrun6e',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'karma' => 'integer',
        'karma_total' => 'integer',
        'owner' => AsEmail::class,
        'nuyen' => 'integer',
    ];

    /** @var list<string> */
    protected $fillable = [
        'active_skills',
        'agility',
        'armor',
        'augmentations',
        'body',
        'charisma',
        'complex_forms',
        'contacts',
        'edge',
        'edge_current',
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
        'spells',
        'strength',
        'tradition',
        'vehicles',
        'weapons',
        'willpower',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->handle ?? $this->name ?? 'Unnamed Character';
    }

    protected function activeSkills(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: static function (array|null $active_skills): array {
                if (null === $active_skills) {
                    return [];
                }
                array_walk(
                    $active_skills,
                    static function (array &$skill): void {
                        // @phpstan-ignore larastan.noModelMake, argument.type
                        $skill = ActiveSkill::make($skill);
                    },
                );
                return $active_skills;
            },
        );
    }

    protected function agility(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $agility): Attribute {
                return new Attribute($agility, $this);
            },
        );
    }

    protected function body(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $body): Attribute {
                return new Attribute($body, $this);
            },
        );
    }

    /**
     * Force this model to only load for Shadowrun 6E characters.
     * @codeCoverageIgnore
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'shadowrun6e',
            static function (Builder $builder): void {
                $builder->where('system', 'shadowrun6e');
            }
        );
    }

    protected function charisma(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $charisma): Attribute {
                return new Attribute($charisma, $this);
            },
        );
    }

    protected function composure(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->charisma->value + $this->willpower->value;
            },
        );
    }

    protected function drainDice(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int|null {
                if (null === $this->tradition) {
                    return null;
                }
                $attributes = $this->tradition->drain_attributes;
                return (new Attribute($this->attributes[$attributes[0]], $this))->value
                    + (new Attribute($this->attributes[$attributes[1]], $this))->value;
            },
        );
    }

    protected function edge(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $edge): Attribute {
                return new Attribute($edge, $this);
            },
        );
    }

    protected function edgeCurrent(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $edge_current): Attribute {
                if (null === $edge_current) {
                    return $this->edge;
                }
                return new Attribute($edge_current, $this);
            },
        );
    }

    protected function initiativeBase(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->intuition->value + $this->reaction->value;
            },
        );
    }

    protected function initiativeDice(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: static function (): int {
                return 1;
            },
        );
    }

    protected function intuition(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $intuition): Attribute {
                return new Attribute($intuition, $this);
            },
        );
    }

    public function isAwakened(): bool
    {
        if ($this->isMagical()) {
            return true;
        }
        return $this->isTechnomancer();
    }

    public function isMagical(): bool
    {
        return null !== $this->magic;
    }

    public function isTechnomancer(): bool
    {
        return null !== $this->resonance;
    }

    protected function judgeIntentions(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->intuition->value + $this->willpower->value;
            },
        );
    }

    protected function lift(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->body->value + $this->willpower->value;
            },
        );
    }

    protected function logic(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $logic): Attribute {
                return new Attribute($logic, $this);
            },
        );
    }

    protected function magic(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $magic): Attribute|null {
                if (null === $magic) {
                    return null;
                }
                return new Attribute($magic, $this);
            },
        );
    }

    protected function memory(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->intuition->value + $this->logic->value;
            },
        );
    }

    /**
     * @codeCoverageIgnore
     */
    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    protected function qualities(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null $qualities): array {
                if (null === $qualities) {
                    return [];
                }
                array_walk($qualities, function (array &$quality): void {
                    try {
                        $quality = Quality::findOrFail((string)$quality['id']);
                    } catch (ModelNotFoundException) {
                        Log::warning(
                            'Shadowrun 6E character "{name}" ({id}) has invalid quality ID "{quality}"',
                            [
                                'name' => $this->handle,
                                'id' => $this->id,
                                'quality' => $quality['id'],
                            ],
                        );
                        $quality = null;
                    }
                });
                return array_filter($qualities, static function (Quality|null $quality): bool {
                    return $quality instanceof Quality;
                });
            },
        );
    }

    protected function reaction(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $reaction): Attribute {
                return new Attribute($reaction, $this);
            },
        );
    }

    protected function resonance(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $resonance): Attribute|null {
                if (null === $resonance) {
                    return null;
                }
                return new Attribute($resonance, $this);
            },
        );
    }

    protected function strength(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $strength): Attribute {
                return new Attribute($strength, $this);
            },
        );
    }

    protected function surpriseDice(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return $this->intuition->value + $this->reaction->value;
            },
        );
    }

    protected function tradition(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (string|null $tradition): Tradition|null {
                if (null === $tradition) {
                    return null;
                }
                return Tradition::findOrFail($tradition);
            },
        );
    }

    protected function willpower(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $willpower): Attribute {
                return new Attribute($willpower, $this);
            },
        );
    }
}
