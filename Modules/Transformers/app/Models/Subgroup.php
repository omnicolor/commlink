<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use Modules\Transformers\Enums\Classification;
use Override;
use RuntimeException;

use function sprintf;
use function strtolower;

class Subgroup
{
    public Classification $class;

    public int $cost;

    public string $description;

    public string $id;
    public string $name;

    /**
     * @var array<string, mixed>
     */
    public array $requirements;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $subgroups;

    public function __construct(string $id)
    {
        $filename = config('transformers.data_path') . 'subgroups.php';
        self::$subgroups ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$subgroups[$this->id])) {
            throw new RuntimeException(sprintf(
                'Subgroup ID "%s" is invalid',
                $id
            ));
        }

        $group = self::$subgroups[$id];
        $this->class = $group['class'];
        $this->cost = $group['cost'];
        $this->description = $group['description'];
        $this->name = $group['name'];
        $this->requirements = $group['requirements'] ?? [];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
