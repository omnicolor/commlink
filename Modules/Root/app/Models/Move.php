<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\WithoutIncrementing;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;
use stdClass;

use function config;

/**
 * @property string $description
 * @property stdClass $effects
 * @property string $id
 * @property string $name
 * @property bool $weapon_move
 */
#[WithoutIncrementing]
class Move extends Model implements Stringable
{
    use Sushi;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'effects',
        'id',
        'name',
        'weapon_move',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'description' => 'string',
            'effects' => 'object',
            'id' => 'string',
            'name' => 'string',
            'weapon_move' => 'boolean',
        ];
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

    #[Scope]
    protected function move(Builder $query): Builder
    {
        return $query->where('weapon_move', false);
    }

    #[Scope]
    protected function weapon(Builder $query): Builder
    {
        return $query->where('weapon_move', true);
    }
}
