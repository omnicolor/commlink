<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @property-read bool $adept_only
 * @property-read string $description
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class Metamagic extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'adept_only',
        'description',
        'id',
        'name',
        'page',
        'ruleset',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array{
     *     adept_only: bool,
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'metamagics.php';
        return require $filename;
    }
}
