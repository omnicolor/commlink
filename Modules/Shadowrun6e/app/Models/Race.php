<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Modules\Shadowrun6e\ValueObjects\BaselineAttribute;
use Override;
use Stringable;
use Sushi\Sushi;
use stdClass;

use function config;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read int $agi_max
 * @property-read int $agi_min
 * @property-read BaselineAttribute $agility
 * @property-read int $bod_max
 * @property-read int $bod_min
 * @property-read BaselineAttribute $body
 * @property-read int $cha_max
 * @property-read int $cha_min
 * @property-read BaselineAttribute $charisma
 * @property-read int $dermal_armor
 * @property-read string $description
 * @property-read int $edg_max
 * @property-read int $edg_min
 * @property-read BaselineAttribute $edge
 * @property-read string $id
 * @property-read int $int_max
 * @property-read int $int_min
 * @property-read BaselineAttribute $intuition
 * @property-read int $log_max
 * @property-read int $log_min
 * @property-read BaselineAttribute $logic
 * @property-read string $name
 * @property-read int $page
 * @property-read int $rea_max
 * @property-read int $rea_min
 * @property-read BaselineAttribute $reaction
 * @property-read int $reach
 * @property-read string $ruleset
 * @property-read array{A?: int, B?: int, C: int, D: int, E: int} $special_points
 * @property-read int $str_max
 * @property-read int $str_min
 * @property-read BaselineAttribute $strength
 * @property-read string|null $vision
 * @property-read int $wil_max
 * @property-read int $wil_min
 * @property-read BaselineAttribute $willpower
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

    protected function agility(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->agi_min,
                    $this->agi_max,
                    'agility',
                );
            },
        );
    }

    protected function body(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->bod_min,
                    $this->bod_max,
                    'body',
                );
            },
        );
    }

    protected function charisma(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->cha_min,
                    $this->cha_max,
                    'charisma',
                );
            },
        );
    }

    protected function edge(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->edg_min,
                    $this->edg_max,
                    'edge',
                );
            },
        );
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
        $filename = config('shadowrun6e.data_path') . 'races.php';
        return require $filename;
    }

    protected function intuition(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->int_min,
                    $this->int_max,
                    'intuition',
                );
            },
        );
    }

    protected function logic(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->log_min,
                    $this->log_max,
                    'logic',
                );
            },
        );
    }

    protected function reaction(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->rea_min,
                    $this->rea_max,
                    'reaction',
                );
            },
        );
    }

    protected function specialPoints(): Attribute
    {
        return Attribute::make(
            get: static function (string $points): stdClass {
                return json_decode($points, flags: JSON_THROW_ON_ERROR);
            },
        );
    }

    protected function strength(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->str_min,
                    $this->str_max,
                    'strength',
                );
            },
        );
    }

    protected function willpower(): Attribute
    {
        return Attribute::make(
            get: function (): BaselineAttribute {
                return new BaselineAttribute(
                    $this->wil_min,
                    $this->wil_max,
                    'willpower',
                );
            },
        );
    }
}
