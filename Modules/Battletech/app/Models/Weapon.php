<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Battletech\Enums\AvailabilityRating;
use Modules\Battletech\Enums\DamageType;
use Modules\Battletech\Enums\EquipmentAffiliation;
use Modules\Battletech\Enums\LegalityRating;
use Modules\Battletech\Enums\TechnologyRating;
use Modules\Battletech\Enums\WeaponType;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function is_int;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read EquipmentAffiliation $affiliation
 * @property-read int $armor_penetration
 * @property-read int $attack_roll
 * @property-read array<int, AvailabilityRating> $availability
 * @property-read int $base_damage
 * @property-read int $cost
 * @property-read int|null $cost_reload
 * @property-read array<int, DamageType> $damage_effects
 * @property-read LegalityRating $legality
 * @property-read int $mass Mass in grams.
 * @property-read int|null $mass_reload Mass of a reload in grams.
 * @property-read string $name
 * @property-read string|null $notes
 * @property-read int $page
 * @property-read array<int, int|string> $range Ranges in meters.
 * @property-read string $ruleset
 * @property-read int|null $shots
 * @property-read TechnologyRating $tech_level
 * @property-read WeaponType $type
 */
class Weapon extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'cost_reload' => 'int',
        'mass_reload' => 'int',
        'shots' => 'int',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function affiliation(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $affiliation): EquipmentAffiliation {
                if (null === $affiliation) {
                    return EquipmentAffiliation::General;
                }
                return EquipmentAffiliation::from($affiliation);
            },
        );
    }

    protected function availability(): Attribute
    {
        return Attribute::make(
            get: static function (string $availability): array {
                $availability = json_decode($availability, true, JSON_THROW_ON_ERROR);
                foreach ($availability as &$rating) {
                    $rating = AvailabilityRating::from($rating);
                }
                return $availability;
            },
        );
    }

    protected function damageEffects(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $damage_effects): array {
                if (null === $damage_effects) {
                    return [];
                }
                $damage_effects = json_decode($damage_effects);
                foreach ($damage_effects as &$effect) {
                    $effect = DamageType::from($effect);
                }
                return $damage_effects;
            },
        );
    }

    /**
     * @return array{
     *     affiliation: string|null,
     *     armor_penetration: int,
     *     attack_roll: int|null,
     *     availability: string,
     *     base_damage: int,
     *     cost: int,
     *     damage_effect: string|null,
     *     id: string,
     *     legality: string,
     *     mass: int,
     *     mass_reload: int|null,
     *     name: string,
     *     notes: string|null,
     *     page: int,
     *     range: int,
     *     reload: int|null,
     *     ruleset: string,
     *     shots: int|null,
     *     tech_level: string,
     *     weapon_type: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('battletech.data_path') . 'weapons.php';
        return require $filename;
    }

    protected function legality(): Attribute
    {
        return Attribute::make(
            get: static function (string $legality): LegalityRating {
                return LegalityRating::from($legality);
            },
        );
    }

    protected function range(): Attribute
    {
        return Attribute::make(
            get: static function (string|int $range): array {
                if (is_int($range)) {
                    return [$range];
                }
                return json_decode($range, true, JSON_THROW_ON_ERROR);
            },
        );
    }

    protected function techLevel(): Attribute
    {
        return Attribute::make(
            get: static function (string $tech_level): TechnologyRating {
                return TechnologyRating::from($tech_level);
            },
        );
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: function (): WeaponType {
                return WeaponType::from($this->attributes['weapon_type']);
            },
        );
    }
}
