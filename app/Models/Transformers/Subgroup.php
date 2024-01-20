<?php

declare(strict_types=1);

namespace App\Models\Transformers;

use RuntimeException;

use function strtolower;

class Subgroup
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public Classification $class;

    /** @psalm-suppress PossiblyUnusedProperty */
    public int $cost;

    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;

    public string $id;
    public string $name;

    /**
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<string, mixed>
     */
    public array $requirements;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $subgroups;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.transformers') . 'subgroups.php';
        self::$subgroups ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$subgroups[$this->id])) {
            throw new RuntimeException(\sprintf(
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

    public function __toString(): string
    {
        return $this->name;
    }
}
