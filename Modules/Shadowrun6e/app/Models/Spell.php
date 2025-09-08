<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use DomainException;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Shadowrun6e\Enums\DamageType;
use Modules\Shadowrun6e\Enums\SpellAdjustment;
use Modules\Shadowrun6e\Enums\SpellCategory;
use Modules\Shadowrun6e\Enums\SpellDuration;
use Modules\Shadowrun6e\Enums\SpellRange;
use Modules\Shadowrun6e\Enums\SpellType;
use Modules\Shadowrun6e\ValueObjects\Damage;
use Override;
use Stringable;
use Sushi\Sushi;

use function array_walk;
use function config;
use function is_numeric;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read SpellCategory $category
 * @property-read array<int, DamageType>|null $damage
 * @property-read string $description
 * @property-read int $drain_value
 * @property-read SpellDuration|int $duration
 * @property-read string $id
 * @property-read bool $indirect
 * @property-read string $name
 * @property-read int $page
 * @property-read SpellRange $range
 * @property-read string $ruleset
 * @property-read SpellType $type
 */
class Spell extends Model implements Stringable
{
    use Sushi;

    /** @var array<int, SpellAdjustment> */
    private array $adjustments = [];
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'category',
        'damage',
        'description',
        'drain_value',
        'duration',
        'id',
        'indirect',
        'name',
        'page',
        'range',
        'type',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    public function adjust(SpellAdjustment $adjustment): self
    {
        if (
            SpellCategory::Combat !== $this->category
            && SpellAdjustment::AmpUp === $adjustment
        ) {
            throw new DomainException('Only combat spells can be amped up');
        }
        $this->adjustments[] = $adjustment;
        return $this;
    }

    /**
     * @return array<string, class-string|string>
     */
    protected function casts(): array
    {
        return [
            'category' => SpellCategory::class,
            'indirect' => 'bool',
            'range' => SpellRange::class,
            'type' => SpellType::class,
        ];
    }

    protected function damage(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $damage): array|null {
                if (null === $damage) {
                    return null;
                }
                $damage = json_decode($damage, true, flags: JSON_THROW_ON_ERROR);
                array_walk($damage, static function (string &$value): void {
                    $value = DamageType::from($value);
                });
                return $damage;
            },
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: static function (int|string $duration): SpellDuration|int {
                if (is_numeric($duration)) {
                    return (int)$duration;
                }
                return SpellDuration::from($duration);
            },
        );
    }

    public function getDamage(
        Character $caster,
        int $net_hits,
    ): Damage|null {
        if (SpellCategory::Combat !== $this->category) {
            return null;
        }

        if ($this->indirect && null !== $caster->magic) {
            $net_hits += (int)ceil($caster->magic->value / 2);
        }

        foreach ($this->damage ?? [] as $damage) {
            if (DamageType::Special === $damage) {
                continue;
            }
            foreach ($this->adjustments as $adjustment) {
                if (SpellAdjustment::AmpUp === $adjustment) {
                    ++$net_hits;
                }
            }
            return new Damage($damage, $net_hits);
        }

        return null; // @codeCoverageIgnore
    }

    /**
     * @return array{
     *     category: SpellCategory,
     *     damage: string|null,
     *     description: string,
     *     drain_value: int,
     *     duration: SpellDuration,
     *     id: string,
     *     indirect: bool,
     *     name: string,
     *     page: int,
     *     range: SpellRange,
     *     ruleset: string,
     *     type: SpellType
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'spells.php';
        return require $filename;
    }
}
