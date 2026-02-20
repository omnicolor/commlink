<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Facades\App\Services\DiceService;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Enums\WeaponType;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function is_numeric;
use function sprintf;

/**
 * @method static Builder melee()
 * @method static Builder missile()
 * @property-read int $cost
 * @property string $damage
 * @property string $id
 * @property string $name
 * @property string $notes
 * @property string|null $other_names
 * @property int $page
 * @property int|string $price
 * @property int|null $range
 * @property string $ruleset
 * @property int $tech_cost
 * @property TechStrata $tech_strata
 * @property WeaponType $type
 */
class Weapon extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    #[Override]
    public function __toString(): string
    {
        return $this->attributes['name'];
    }

    /**
     * @return array{
     *     damage: string,
     *     id: string,
     *     name: string,
     *     notes: string,
     *     other_names: string|null,
     *     page: int,
     *     price: int|string,
     *     range: int|null,
     *     ruleset: string,
     *     tech_cost: int,
     *     tech_strata: TechStrata,
     *     type: WeaponType
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'weapons.php';
        return require $filename;
    }

    public function cost(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $cost = $this->attributes['price'];
                if (is_numeric($cost)) {
                    return (int)$cost;
                }

                return DiceService::rollDice($cost)->total;
            },
        );
    }

    public static function findByOtherName(string $name): self
    {
        return self::where('other_names', 'like', sprintf('%%%s%%', $name))
            ->firstOrFail();
    }

    #[Scope]
    protected function melee(Builder $query): void
    {
        $query->where('type', WeaponType::Melee);
    }

    #[Scope]
    protected function missile(Builder $query): void
    {
        $query->where('type', WeaponType::Missile);
    }

    protected function techStrata(): Attribute
    {
        return Attribute::make(
            get: function (string $value): TechStrata {
                return TechStrata::from($value);
            },
        );
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: function (string $value): WeaponType {
                return WeaponType::from($value);
            },
        );
    }
}
