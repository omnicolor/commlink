<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Dnd5e\Enums\Ability;
use Override;
use Stringable;
use Sushi\Sushi;

use function array_walk;
use function config;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read string $description
 * @property-read int $hit_die
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read Ability $primary_ability
 * @property-read string $ruleset
 * @property-read array<int, Ability> $saving_throw_proficiencies
 */
class Role extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     description: string,
     *     hit_die: int,
     *     name: string,
     *     page: int,
     *     primary_ability: Ability,
     *     ruleset: string,
     *     saving_throw_proficiencies: string
     * }>
     */
    public function getRows(): array
    {
        $filename = config('dnd5e.data_path') . 'classes.php';
        return require $filename;
    }

    protected function primaryAbility(): Attribute
    {
        return Attribute::make(
            get: static function (string $ability): Ability {
                return Ability::from($ability);
            },
        );
    }

    protected function savingThrowProficiencies(): Attribute
    {
        return Attribute::make(
            get: static function (string $proficiencies): array {
                $proficiencies = json_decode(
                    $proficiencies,
                    true,
                    JSON_THROW_ON_ERROR,
                );
                array_walk(
                    $proficiencies,
                    static function (string &$proficiency): void {
                        $proficiency = Ability::from($proficiency);
                    },
                );
                return $proficiencies;
            },
        );
    }
}
