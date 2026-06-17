<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Stillfleet\Enums\TechStrata;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;

/**
 * @property-read int $cost
 * @property int $damage_reduction
 * @property string $id
 * @property string $name
 * @property string $notes
 * @property int $page
 * @property string $ruleset
 * @property int $tech_cost
 * @property TechStrata $tech_strata
 */
class Armor extends Model implements Stringable
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
     *     cost: int,
     *     damage_reduction: int,
     *     id: string,
     *     name: string,
     *     notes: string,
     *     page: int,
     *     ruleset: string,
     *     tech_cost: int,
     *     tech_strata: TechStrata
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'armor.php';
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
}
