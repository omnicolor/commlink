<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Avatar\Enums\TechniqueClass;
use Modules\Avatar\Enums\TechniqueLevel;
use Modules\Avatar\Enums\TechniqueType;
use Override;
use Stringable;
use Sushi\Sushi;

use function config;

/**
 * @property TechniqueClass $class
 * @property string $description
 * @property string $id
 * @property string $name
 * @property int $page
 * @property bool $rare
 * @property string $ruleset
 * @property string $specialization
 * @property TechniqueType $type
 */
class Technique extends Model implements Stringable
{
    use Sushi;

    public $incrementing = false;
    protected $keyType = 'string';

    public TechniqueLevel $level;

    /** @var array<string, class-string|string> */
    protected $casts = [
        'class' => TechniqueClass::class,
        'description' => 'string',
        'id' => 'string',
        'name' => 'string',
        'page' => 'int',
        'rare' => 'bool',
        'ruleset' => 'string',
        'specialization' => 'string',
        'type' => TechniqueType::class,
    ];

    /** @var list<string> */
    protected $fillable = [
        'class',
        'description',
        'id',
        'name',
        'page',
        'rare',
        'ruleset',
        'specialization',
        'type',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, array{
     *   class: TechniqueClass,
     *   description: string,
     *   id: string,
     *   name: string,
     *   page: int,
     *   rare?: bool,
     *   ruleset: string,
     *   specialization?: string|null,
     *   type: TechniqueType,
     * }>
     */
    public function getRows(): array
    {
        $filename = config('avatar.data_path') . 'techniques.php';
        return require $filename;
    }
}
