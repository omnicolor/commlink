<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;
use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read string $description
 * @property-read array<string, mixed>|null $effects
 * @property-read string $id
 * @property-read int $karma_cost
 * @property-read int|string|null $level
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class Quality extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'effects',
        'id',
        'karma_cost',
        'level',
        'name',
        'page',
        'ruleset',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function effects(): Attribute
    {
        return Attribute::make(
            get: static function (string|null $effects): array {
                if (null === $effects) {
                    return [];
                }
                return json_decode($effects, true, flags: JSON_THROW_ON_ERROR);
            },
        );
    }

    /**
     * @return array{
     *     description: string,
     *     effects: string|null,
     *     karma_cost: int,
     *     level: int|string|null,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'qualities.php';
        return require $filename;
    }
}
