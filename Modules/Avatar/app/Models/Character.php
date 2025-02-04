<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Avatar\Database\Factories\CharacterFactory;
use Stringable;

/**
 * @property string $appearance
 * @property-read Background $background
 * @property-write Background|string $background
 * @property int<-3, 3> $balance
 * @property-read array<int, Condition> $conditions
 * @property-write array<int, Condition|string> $conditions
 * @property int<-1, 4> $creativity
 * @property array<int, string> $demeanors
 * @property int<0, 5> $fatigue
 * @property string $fighting_style
 * @property int<-1, 4> $focus
 * @property int<-1, 4> $harmony
 * @property string $history
 * @property-read array<int, Move> $moves
 * @property-write array<int, Move|string> $moves
 * @property string $name
 * @property int<-1, 4> $passion
 * @property-read Playbook $playbook
 * @property-write Playbook|string $playbook
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'avatar',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'appearance',
        'background',
        'balance',
        'conditions',
        'creativity',
        'demeanors',
        'fatigue',
        'fighting_style',
        'focus',
        'harmony',
        'history',
        'moves',
        'name',
        'passion',
        'playbook',
        //'statuses',
        //'techniques',
        'training',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return (string)($this->attributes['name'] ?? 'Unnamed character');
    }

    /**
     * Force this model to only load for Avatar characters.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(
            'avatar',
            function (Builder $builder): void {
                $builder->where('system', 'avatar');
            }
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function background(): Attribute
    {
        return Attribute::make(
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

    public function conditions(): Attribute
    {
        return Attribute::make(
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

    public function creativity(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->playbook->creativity->value
                    + ($this->attributes['creativity'] ?? 0);
            },
            set: function (int $creativity): int {
                $this->attributes['creativity'] = $creativity;
                return $creativity;
            },
        );
    }

    public function fatigue(): Attribute
    {
        return Attribute::make(
            get: function (?int $fatigue): int {
                return $fatigue ?? 0;
            },
        );
    }

    public function focus(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->playbook->focus->value
                    + ($this->attributes['focus'] ?? 0);
            },
            set: function (int $focus): int {
                $this->attributes['focus'] = $focus;
                return $focus;
            },
        );
    }

    public function harmony(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->playbook->harmony->value
                    + ($this->attributes['harmony'] ?? 0);
            },
            set: function (int $harmony): int {
                $this->attributes['harmony'] = $harmony;
                return $harmony;
            },
        );
    }

    public function moves(): Attribute
    {
        return Attribute::make(
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

    public function passion(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                return $this->playbook->passion->value
                    + ($this->attributes['passion'] ?? 0);
            },
            set: function (int $passion): int {
                $this->attributes['passion'] = $passion;
                return $passion;
            },
        );
    }

    public function playbook(): Attribute
    {
        return Attribute::make(
            get: function (): Playbook {
                return new Playbook($this->attributes['playbook']);
            },
            set: function (string | Playbook $playbook): string {
                if ($playbook instanceof Playbook) {
                    return $playbook->id;
                }
                return $playbook;
            },
        );
    }

    public function training(): Attribute
    {
        return Attribute::make(
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
