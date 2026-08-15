<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;

/**
 * @property-read string $advantage_all
 * @property-read string $advantage_magician
 * @property-read string $advantage_adept
 * @property-read array{all: string, magician: string, adept: string} $advantages
 * @property-read string $description
 * @property-read string $disadvantages
 * @property-read string $id
 * @property-read string $name
 * @property-read int $page
 * @property-read string $ruleset
 */
class MentorSpirit extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    /** @var list<string> */
    protected $fillable = [
        'advantage_all',
        'advantage_magician',
        'advantage_adept',
        'description',
        'disadvantages',
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

    protected function advantages(): Attribute
    {
        return Attribute::make(
            get: function (): array {
                return [
                    'all' => $this->attributes['advantage_all'],
                    'magician' => $this->attributes['advantage_magician'],
                    'adept' => $this->attributes['advantage_adept'],
                ];
            }
        );
    }

    /**
     * @return array{
     *     advantage_all: string,
     *     advantage_magician: string,
     *     advantage_adept: string,
     *     description: string,
     *     disadvantages: string,
     *     id: string,
     *     name: string,
     *     page: int,
     *     ruleset: string
     * }
     */
    public function getRows(): array
    {
        $filename = config('shadowrun6e.data_path') . 'mentor-spirits.php';
        return require $filename;
    }
}
