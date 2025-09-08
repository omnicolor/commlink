<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Stillfleet\Enums\AdvancedPowersCategory;
use Override;
use Stringable;
use Sushi\Sushi;

use function array_merge;
use function array_walk;
use function config;
use function json_decode;

/**
 * The character's class (job, vocation, role).
 * @property-read array<int, string> $advanced_powers_lists
 * @property-read string $description
 * @property-read array<int, string> $grit
 * @property string $id
 * @property-read Power $marquee_power
 * @property-read string $name
 * @property-read int $optional_choices
 * @property-read array<int, Power> $optional_powers
 * @property-read array<int, Power> $other_powers
 * @property-read int $page
 * @property-read array<int, Power> $powers
 * @property-read array<int, string> $responsibilities
 * @property-read string $ruleset
 */
class Role extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var array<int|string, Power> */
    public array $added_powers = [];
    public int $level = 1;

    #[Override]
    public function __toString(): string
    {
        return $this->attributes['name'];
    }

    public function addPowers(Power ...$powers): self
    {
        $this->added_powers += $powers;
        return $this;
    }

    /**
     * @return array{
     *     description: string,
     *     grit: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     optional_choices: int,
     *     power_advanced: string,
     *     power_marque: string,
     *     power_optional: string,
     *     power_other: string,
     *     responsibilities: string,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'roles.php';
        return require $filename;
    }

    public function grit(): Attribute
    {
        return Attribute::make(
            get: function (string $value): array {
                return json_decode($value, true);
            },
        );
    }

    public function advancedPowersLists(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $lists = json_decode($this->attributes['power_advanced'], true);
                array_walk($lists, static function (&$list): void {
                    $list = AdvancedPowersCategory::from($list);
                });
                return $lists;
            },
        );
    }

    public function marqueePower(): Attribute
    {
        return Attribute::make(
            get: function (): Power {
                // @phpstan-ignore return.type
                return Power::findOrFail($this->attributes['power_marquee']);
            },
        );
    }

    public function optionalPowers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $powers = json_decode($this->attributes['power_optional'], true);
                array_walk($powers, static function (&$power): void {
                    $power = Power::findOrFail($power);
                });
                return $powers;
            },
        );
    }

    public function otherPowers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $powers = json_decode($this->attributes['power_other'], true);
                array_walk($powers, static function (string &$power): void {
                    $power = Power::findOrFail($power);
                });
                return $powers;
            }
        );
    }

    public function powers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return array_merge(
                    [$this->marquee_power],
                    $this->other_powers,
                    $this->added_powers,
                );
            }
        );
    }

    public function responsibilities(): Attribute
    {
        return Attribute::make(
            get: function (string $responsibilities): array {
                return json_decode($responsibilities, true);
            }
        );
    }
}
