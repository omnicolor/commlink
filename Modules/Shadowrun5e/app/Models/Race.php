<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Illuminate\Database\Eloquent\Model;
use Override;
use RuntimeException;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;
use function strtoupper;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read int $agi_max
 * @property-read int $agi_min
 * @property-read int $bod_max
 * @property-read int $bod_min
 * @property-read int $cha_max
 * @property-read int $cha_min
 * @property-read int $dermal_armor
 * @property-read string $description
 * @property-read int $edg_max
 * @property-read int $edg_min
 * @property-read string $id
 * @property-read int $int_max
 * @property-read int $int_min
 * @property-read float $lifestyle_cost_modifier
 * @property-read int $log_max
 * @property-read int $log_min
 * @property-read string $name
 * @property-read int $page
 * @property-read int $rea_max
 * @property-read int $rea_min
 * @property-read int $reach
 * @property-read string $ruleset
 * @property-read string $special_points
 * @property-read int $str_max
 * @property-read int $str_min
 * @property-read string|null $vision
 * @property-read int $wil_max
 * @property-read int $wil_min
 */
class Race extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'agi_max',
        'agi_min',
        'bod_max',
        'bod_min',
        'cha_max',
        'cha_min',
        'dermal_armor',
        'description',
        'edg_max',
        'edg_min',
        'id',
        'int_max',
        'int_min',
        'lifestyle_cost_modifier',
        'log_max',
        'log_min',
        'name',
        'page',
        'rea_max',
        'rea_min',
        'reach',
        'ruleset',
        'special_points',
        'str_max',
        'str_min',
        'vision',
        'wil_max',
        'wil_min',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, array{
     *     agi_max: int,
     *     agi_min: int,
     *     bod_max: int,
     *     bod_min: int,
     *     cha_max: int,
     *     cha_min: int,
     *     dermal_armor: int,
     *     description: string,
     *     edg_max: int,
     *     edg_min: int,
     *     id: string,
     *     int_max: int,
     *     int_min: int,
     *     lifestyle_cost_modifier: float,
     *     log_max: int,
     *     log_min: int,
     *     name: string,
     *     page: int,
     *     rea_max: int,
     *     rea_min: int,
     *     reach: int,
     *     ruleset: string,
     *     special_points: string,
     *     str_max: int,
     *     str_min: int,
     *     vision: string|null,
     *     wil_max: int,
     *     wil_min: int
     * }>
     */
    public function getRows(): array
    {
        $filename = config('shadowrun5e.data_path') . 'races.php';
        return require $filename;
    }

    public function getSpecialPointsForPriority(string $priority): int
    {
        $priority = strtoupper($priority);
        $priorities = json_decode($this->special_points, true, JSON_THROW_ON_ERROR);
        if (!isset($priorities[$priority])) {
            throw new RuntimeException('Invalid priority');
        }
        return $priorities[$priority];
    }
}
