<?php

declare(strict_types=1);

namespace Modules\Root\Models;

use Illuminate\Database\Eloquent\Model;
use Stringable;
use Sushi\Sushi;

/**
 * @property string $description
 * @property string $id
 * @property string $name
 */
class Nature extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * @var array<string, class-string|string>
     */
    protected $casts = [
        'description' => 'string',
        'id' => 'string',
        'name' => 'string',
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'description',
        'id',
        'name',
    ];

    /**
     * @return array{
     *     description: string,
     *     id: string,
     *     name: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('root.data_path') . 'natures.php';
        return require $filename;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
