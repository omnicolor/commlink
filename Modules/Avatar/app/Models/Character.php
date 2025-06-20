<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use App\Casts\AsEmail;
use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Avatar\Database\Factories\CharacterFactory;
use Modules\Avatar\Enums\Background;
use Modules\Avatar\Enums\Condition;
use Modules\Avatar\Enums\TechniqueLevel;
use Modules\Avatar\Enums\Training;
use Modules\Avatar\ValueObjects\Attribute;
use Modules\Avatar\ValueObjects\GrowthAdvancements;
use Override;
use Stringable;

use function array_map;
use function get_class;

/**
 * @property string $appearance
 * @property-read Background $background
 * @property-write Background|string $background
 * @property int<-3, 3> $balance
 * @property-read array<int, Condition> $conditions
 * @property-write array<int, Condition|string> $conditions
 * @property array{0: string, 1: string} $connections
 * @property Attribute $creativity
 * @property array<int, string> $demeanors
 * @property int<0, 5> $fatigue
 * @property string $fighting_style
 * @property Attribute $focus
 * @property int<0, 4> $growth
 * @property GrowthAdvancements $growth_advancements
 * @property Attribute $harmony
 * @property string $history
 * @property string $home_town
 * @property-read array<int, Move> $moves
 * @property-write array<int, Move|string> $moves
 * @property string $name
 * @property Attribute $passion
 * @property-read Playbook $playbook
 * @property array<string, string> $playbook_options
 * @property-write Playbook|string $playbook
 * @property-read array<int, Status> $statuses
 * @property-write array<int, Status|string> $statuses
 * @property-read array<int, Technique> $techniques
 * @property-write array<int, Technique|array{id: string, level: string}> $techniques
 * @property-read Training $training
 * @property-write Training|string $training
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /** @var array<string, mixed> */
    protected $attributes = [
        'system' => 'avatar',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'owner' => AsEmail::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'appearance',
        'background',
        'balance',
        'conditions',
        'connections',
        'creativity',
        'demeanors',
        'fatigue',
        'fighting_style',
        'focus',
        'growth',
        'growth_advancements',
        'harmony',
        'history',
        'home_town',
        'moves',
        'name',
        'passion',
        'playbook',
        'playbook_options',
        'statuses',
        'techniques',
        'training',
    ];

    /** @var list<string> */
    protected $hidden = [
        '_id',
    ];

    #[Override]
    public function __toString(): string
    {
        return (string)($this->attributes['name'] ?? 'Unnamed character');
    }

    /**
     * Force this model to only load for Avatar characters.
     */
    #[Override]
    protected static function booted(): void
    {
        static::addGlobalScope(
            'avatar',
            function (Builder $builder): void {
                $builder->where('system', 'avatar');
            }
        );
    }

    #[Override]
    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function background(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): Background {
                return Background::from($this->attributes['background']);
            },
            set: function (string | Background $background): string {
                if ($background instanceof Background) {
                    return $background->value;
                }
                return Background::from($background)->value;
            },
        );
    }

    public function conditions(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (?array $conditions): array {
                return array_map(
                    function (string $condition): Condition {
                        return Condition::from($condition);
                    },
                    $conditions ?? [],
                );
            },
            set: function (array $conditions): array {
                foreach ($conditions as $key => $condition) {
                    if ($conditions[$key] instanceof Condition) {
                        $conditions[$key] = $condition->value;
                    }
                }
                return ['conditions' => $conditions];
            },
        );
    }

    public function creativity(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                $creativity = new Attribute($this->attributes['creativity'] ?? 0);
                return $this->playbook->creativity->value + $creativity->value;
            },
            set: function (int $creativity): int {
                $this->attributes['creativity'] = $creativity;
                return $creativity;
            },
        );
    }

    public function fatigue(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (?int $fatigue): int {
                return $fatigue ?? 0;
            },
        );
    }

    public function focus(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                $focus = new Attribute($this->attributes['focus'] ?? 0);
                return $this->playbook->focus->value + $focus->value;
            },
            set: function (int $focus): int {
                $this->attributes['focus'] = $focus;
                return $focus;
            },
        );
    }

    public function growthAdvancements(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): GrowthAdvancements {
                return new GrowthAdvancements(
                    $this->attributes['growth_advancements'] ?? [],
                );
            },
        );
    }

    public function harmony(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                $harmony = new Attribute($this->attributes['harmony'] ?? 0);
                return $this->playbook->harmony->value + $harmony->value;
            },
            set: function (int $harmony): int {
                $this->attributes['harmony'] = $harmony;
                return $harmony;
            },
        );
    }

    public function moves(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): array {
                $moves = [];
                foreach ($this->attributes['moves'] ?? [] as $move) {
                    $moves[] = new Move($move);
                }
                return $moves;
            },
            set: function (array $moves): array {
                foreach ($moves as $key => $move) {
                    if ($moves[$key] instanceof Move) {
                        $moves[$key] = $move->id;
                    }
                }
                return ['moves' => $moves];
            },
        );
    }

    public function passion(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                $passion = new Attribute($this->attributes['passion'] ?? 0);
                return $this->playbook->passion->value + $passion->value;
            },
            set: function (int $passion): int {
                $this->attributes['passion'] = $passion;
                return $passion;
            },
        );
    }

    public function playbook(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): Playbook {
                $playbook = new Playbook($this->attributes['playbook']);
                if (isset($this->attributes['playbook_options'])) {
                    $class = get_class($playbook->feature);
                    $playbook->feature = new $class($this->attributes['playbook_options']);
                }
                return $playbook;
            },
            set: function (string | Playbook $playbook): string {
                if ($playbook instanceof Playbook) {
                    return $playbook->id;
                }
                return $playbook;
            },
        );
    }

    public function statuses(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): array {
                $statuses = [];
                foreach ($this->attributes['statuses'] ?? [] as $status) {
                    $statuses[] = new Status($status);
                }
                return $statuses;
            },
            set: function (array $statuses): array {
                foreach ($statuses as $key => $status) {
                    if ($statuses[$key] instanceof Status) {
                        $statuses[$key] = $status->id;
                    }
                }
                return ['statuses' => $statuses];
            },
        );
    }

    public function techniques(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): array {
                $techniques = [];
                foreach ($this->attributes['techniques'] ?? [] as $technique) {
                    /** @var Technique */
                    $temp = Technique::findOrFail($technique['id']);
                    $temp->level = TechniqueLevel::from($technique['level']);
                    $techniques[] = $temp;
                }
                return $techniques;
            },
            set: function (array $techniques): array {
                foreach ($techniques as $key => $technique) {
                    if ($techniques[$key] instanceof Technique) {
                        $temp = [
                            'id' => $technique->id,
                            'level' => $technique->level->value,
                        ];
                        $techniques[$key] = $temp;
                    }
                }
                return ['techniques' => $techniques];
            },
        );
    }

    public function training(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): ?Training {
                if (!isset($this->attributes['training'])) {
                    return null;
                }
                return Training::from($this->attributes['training']);
            },
            set: function (string | Training $training): string {
                if ($training instanceof Training) {
                    return $training->value;
                }
                return $training;
            },
        );
    }
}
