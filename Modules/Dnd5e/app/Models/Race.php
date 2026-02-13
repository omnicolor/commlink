<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Dnd5e\Enums\Ability;
use Modules\Dnd5e\Enums\CreatureSize;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @property-read array<Ability, int> $ability_increases
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $parent_race
 * @property-read string $ruleset
 * @property-read CreatureSize $size
 */
class Race extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'ability_increases',
        'id',
        'name',
        'page',
        'ruleset',
        'size',
        'tool_proficiencies',
        'weapon_proficiencies',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, class-string|string>
     */
    protected function casts(): array
    {
        return [
            'size' => CreatureSize::class,
        ];
    }

    /**
     * @return array{
     *     ability_increases: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     parent_race: string,
     *     ruleset: string,
     *     size: string,
     *     tool_proficiencies: string,
     *     weapon_proficiencies: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('dnd5e.data_path') . 'races.php';
        return require $filename;
    }

    protected function abilityIncreases(): Attribute
    {
        return Attribute::make(
            get: function (string $increases): array {
                $increases = json_decode($increases, true);
                foreach (array_keys($increases) as $ability) {
                    Ability::from($ability);
                }
                return $increases;
            },
        );
    }
}
