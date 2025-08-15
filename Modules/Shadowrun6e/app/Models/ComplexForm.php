<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Shadowrun6e\Enums\ComplexFormDuration;
use Override;
use Stringable;
use Sushi\Sushi;

/**
 * @property-read string $description
 * @property-read ComplexFormDuration $duration
 * @property-read int|null $fade_value
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class ComplexForm extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'description',
        'duration',
        'fade_value',
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

    public function casts(): array
    {
        return [
            'duration' => ComplexFormDuration::class,
        ];
    }

    /**
     * @return array<int, array{
     *     description: string,
     *     duration: string,
     *     fade_value: int|null,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }>
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'complex-forms.php';
        return require $filename;
    }
}
