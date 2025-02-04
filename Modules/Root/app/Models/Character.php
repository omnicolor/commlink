<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use App\Models\Character as BaseCharacter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Modules\Root\Casts\AttributeCast;
use Modules\Root\Database\Factories\CharacterFactory;
use Modules\Root\ValueObjects\Attribute;
use Stringable;

use function collect;
use function is_array;
use function json_decode;

/**
 * @property Attribute $charm
 * @property Attribute $cunning
 * @property int<0, 5> $decay
 * @property int<0, 5> $decay_max
 * @property int<0, 5> $exhaustion
 * @property int<0, 5> $exhaustion_max
 * @property Attribute $finesse
 * @property int<0, 5> $injury
 * @property int<0, 5> $injury_max
 * @property string $look
 * @property Attribute $luck
 * @property Attribute $might
 * @property string $name
 * @property-read Collection<string, Move> $moves
 * @property-read Nature|null $nature
 * @property-write Nature|string $nature
 * @property-read Playbook $playbook
 * @property-write Playbook|string $playbook
 * @property string $species
 * @property string $system
 */
class Character extends BaseCharacter implements Stringable
{
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'system' => 'root',
    ];

    /**
     * @var array<string, class-string|string>
     */
    protected $casts = [
        'charm' => AttributeCast::class,
        'cunning' => AttributeCast::class,
        'decay' => 'integer',
        'exhaustion' => 'integer',
        'finesse' => AttributeCast::class,
        'injury' => 'integer',
        'luck' => AttributeCast::class,
        'might' => AttributeCast::class,
        'moves' => 'array',
        'name' => 'string',
        'nature' => 'string',
        'playbook' => 'string',
        'system' => 'string',
    ];

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'charm',
        'cunning',
        'decay',
        'exhaustion',
        'finesse',
        'luck',
        'injury',
        'might',
        'moves',
        'name',
        'nature',
        'playbook',
        'species',
        'system',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        '_id',
    ];

    public function __toString(): string
    {
        return $this->name ?? 'Unnamed character';
    }

    protected static function booted(): void
    {
        static::addGlobalScope(
            'root',
            function (Builder $builder): void {
                $builder->where('system', 'root');
            }
        );
    }

    public function decay(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $decay): int {
                if (null === $decay) {
                    return 0;
                }

                return $decay;
            },
        );
    }

    public function decayMax(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return 4;
            },
        );
    }

    public function exhaustion(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $exhaustion): int {
                if (null === $exhaustion) {
                    return 0;
                }

                return $exhaustion;
            },
        );
    }

    public function exhaustionMax(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return 4;
            },
        );
    }

    public function injury(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int|null $injury): int {
                if (null === $injury) {
                    return 0;
                }

                return $injury;
            },
        );
    }

    public function injuryMax(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (): int {
                return 4;
            },
        );
    }

    public function might(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (int $value): Attribute {
                foreach ($this->moves as $move) {
                    if (isset($move->effects->might)) {
                        return new Attribute(
                            value: $value + $move->effects->might,
                            improved_by_move: true,
                        );
                    }
                }
                return new Attribute($value);
            },
        );
    }

    public function moves(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (array|null|string $value): Collection {
                if (null === $value) {
                    return collect([]);
                }
                /** @var array<int, Move> */
                $moves = [];
                if (!is_array($value)) {
                    $value = json_decode($value);
                }
                foreach ($value as $move) {
                    $moves[] = Move::findOrFail($move);
                }
                return collect($moves)->keyBy('id');
            },
        );
    }

    public function nature(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (null|string $nature): Nature|null {
                return Nature::find($nature);
            },
            set: function (Nature|string $nature): string {
                if ($nature instanceof Nature) {
                    return $nature->id;
                }
                return $nature;
            },
        );
    }

    protected static function newFactory(): Factory
    {
        return CharacterFactory::new();
    }

    public function playbook(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (null|string $playbook): Playbook|null {
                return Playbook::find($playbook);
            },
            set: function (Playbook|string $playbook): string {
                if ($playbook instanceof Playbook) {
                    return $playbook->id;
                }
                return $playbook;
            },
        );
    }
}
