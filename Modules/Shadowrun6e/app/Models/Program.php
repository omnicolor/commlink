<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @property-read string $description
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class Program extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    public bool $running = false;

    /** @var list<string> */
    protected $fillable = [
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
     * @return array<int, array{
     *     description: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }>
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'programs.php';
        return require $filename;
    }
}
