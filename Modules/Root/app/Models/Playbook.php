<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Root\Casts\AttributeCast;
use Modules\Root\ValueObjects\Attribute;
use Override;
use Stringable;
use Sushi\Sushi;

use function collect;
use function config;
use function json_decode;

/**
 * @property Attribute $charm
 * @property Attribute $cunning
 * @property string $description_long
 * @property string $description_short
 * @property Attribute $finesse
 * @property string $id
 * @property Attribute $luck
 * @property Attribute $might
 * @property Collection<string, Move> $moves
 * @property string $name
 * @property array<string, Nature> $natures
 * @property Collection<string, Move> $starting_weapon_moves
 */
class Playbook extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var array<string, class-string|string> */
    protected $casts = [
        'charm' => AttributeCast::class,
        'cunning' => AttributeCast::class,
        'description_long' => 'string',
        'description_short' => 'string',
        'finesse' => AttributeCast::class,
        'luck' => AttributeCast::class,
        'might' => AttributeCast::class,
        'moves' => 'array',
        'name' => 'string',
        'natures' => 'string',
    ];

    /** @var list<string> */
    protected $fillable = [
        'description_long',
        'description_short',
        'name',
        'charm',
        'cunning',
        'finesse',
        'luck',
        'moves',
        'might',
        'natures',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     id: string,
     *     charm: int,
     *     cunning: int,
     *     description_long: string,
     *     description_short: string,
     *     finesse: int,
     *     luck: int,
     *     might: int,
     *     moves: string,
     *     name: string,
     *     natures: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('root.data_path') . 'playbooks.php';
        return require $filename;
    }

    public function moves(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (string $moves): array {
                $moves = json_decode($moves);
                foreach ($moves as $key => $move) {
                    unset($moves[$key]);
                    $moves[$move] = Move::findOrFail($move);
                }
                return $moves;
            },
        );
    }

    public function natures(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (string $natures): array {
                $natures = json_decode($natures);
                foreach ($natures as $key => $nature) {
                    unset($natures[$key]);
                    $natures[$nature] = Nature::findOrFail($nature);
                }
                return $natures;
            },
        );
    }

    /**
     * These are the weapon skills a character can choose in chargen and shown
     * in bold on their playbook sheet.
     */
    public function startingWeaponMoves(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: function (string $value): Collection {
                /** @var array<int, Move> */
                $moves = [];
                foreach (json_decode($value) as $move) {
                    $moves[] = Move::findOrFail($move);
                }
                return collect($moves)->keyBy('id');
            },
        );
    }
}
