<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

use function json_decode;

use const JSON_THROW_ON_ERROR;

/**
 * @property-read array<int, string> $drain_attributes
 * @property-read string $description
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @proeprty-read string $primary_attribute
 * @property-read string $ruleset
 */
class Tradition extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'attributes',
        'description',
        'id',
        'name',
        'page',
        'primary_attribute',
        'ruleset',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    protected function drainAttributes(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return json_decode(
                    $this->attributes['attributes'],
                    true,
                    flags: JSON_THROW_ON_ERROR,
                );
            },
        );
    }

    /**
     * @return array{
     *     attributes: string,
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'traditions.php';
        return require $filename;
    }
}
