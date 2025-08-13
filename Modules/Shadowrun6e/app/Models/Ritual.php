<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @property-read bool $anchored
 * @property-read string $description
 * @property-read string $id
 * @property-read bool $material_link
 * @property-read bool $minion
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 * @property-read bool $spell
 * @property-read bool $spotter
 * @property-read int $threshold
 */
class Ritual extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'anchored',
        'description',
        'id',
        'material_link',
        'minion',
        'name',
        'page',
        'ruleset',
        'spell',
        'spotter',
        'threshold',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     anchored: bool,
     *     description: string,
     *     id: string,
     *     material_link: bool,
     *     minion: bool,
     *     name: string,
     *     page: int,
     *     ruleset: string,
     *     spell: bool,
     *     spotter: bool,
     *     threshold: int
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'rituals.php';
        return require $filename;
    }
}
