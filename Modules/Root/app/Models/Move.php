<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stringable;
use Sushi\Sushi;
use stdClass;

/**
 * @immutable
 * @property string $description
 * @property stdClass $effects
 * @property string $id
 * @property string $name
 * @property bool $weapon_move
 */
class Move extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var array<string, class-string|string>
     */
    protected $casts = [
        'description' => 'string',
        'effects' => 'object',
        'id' => 'string',
        'name' => 'string',
        'weapon_move' => 'boolean',
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'effects',
        'id',
        'name',
        'weapon_move',
    ];

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     description: string,
     *     effects: null|string,
     *     id: string,
     *     name: string,
     *     weapon_move: bool
     * }
     */
    public function getRows(): array
    {
        $filename = config('root.data_path') . 'moves.php';
        return require $filename;
    }

    public function scopeMove(Builder $query): Builder
    {
        return $query->where('weapon_move', false);
    }

    public function scopeWeapon(Builder $query): Builder
    {
        return $query->where('weapon_move', true);
    }
}
