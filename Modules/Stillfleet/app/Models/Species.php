<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

use function array_merge;
use function array_walk;
use function config;
use function json_decode;

/**
 * @property-read string $description
 * @property-read string $languages
 * @property-read string $name
 * @property-read array<int, Power> $optional_powers
 * @property-read int $page
 * @property-read array<int, Power> $powers
 * @property-read int $powers_choose
 * @property-read string $ruleset
 * @property-read array<int, Power> $species_powers
 */
class Species extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';
    /** @var array<int|string, Power> */
    public array $added_powers = [];

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
     *     id: string,
     *     languages: string,
     *     name: string,
     *     page: int,
     *     powers: string,
     *     powers_choose: int,
     *     powers_optional: string,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('stillfleet.data_path') . 'species.php';
        return require $filename;
    }

    public function optionalPowers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $powers = json_decode($this->attributes['powers_optional'], true);
                array_walk($powers, function (&$power): void {
                    $power = Power::findOrFail($power);
                });
                return $powers;
            },
        );
    }

    /**
     * All powers possessed by the character, both innate species powers and
     * those chosen at character generation.
     */
    public function powers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return array_merge(
                    $this->species_powers,
                    $this->added_powers,
                );
            }
        );
    }

    public function speciesPowers(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                $powers = json_decode($this->attributes['powers'], true);
                array_walk($powers, function (&$power): void {
                    $power = Power::findOrFail($power);
                });
                return $powers;
            }
        );
    }
}
