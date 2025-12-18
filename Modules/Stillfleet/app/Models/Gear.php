<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Enums\VoidwareType;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @method static Builder comms()
 * @method static Builder drugs()
 * @method static Builder pets()
 * @method static Builder vehicles()
 * @method static Builder ventureware()
 * @property string $description
 * @property string $id
 * @property string $name
 * @property int $page
 * @property int $price
 * @property string $ruleset
 * @property int $tech_cost
 * @property TechStrata $tech_strata
 * @property VoidwareType $type
 */
class Gear extends Model implements Stringable
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
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     price: int,
     *     ruleset: string,
     *     tech_cost: int,
     *     tech_strata: TechStrata,
     *     type: VoidwareType
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'gear.php';
        return require $filename;
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
            get: function (string $value): VoidwareType {
                return VoidwareType::from($value);
            },
        );
    }

    #[Scope]
    protected function comms(Builder $query): void
    {
        $query->where('type', VoidwareType::Comm);
    }

    #[Scope]
    protected function drugs(Builder $query): void
    {
        $query->where('type', VoidwareType::Drug);
    }

    #[Scope]
    protected function pets(Builder $query): void
    {
        $query->where('type', VoidwareType::Pet);
    }

    #[Scope]
    protected function vehicles(Builder $query): void
    {
        $query->where('type', VoidwareType::Vehicle);
    }

    #[Scope]
    protected function ventureware(Builder $query): void
    {
        $query->where('type', VoidwareType::Ventureware);
    }
}
