<?php

declare(strict_types=1);

namespace Modules\Transformers\Models;

use RuntimeException;
use Stringable;

use function strtolower;

/**
 * @psalm-suppress PossiblyUnusedProperty
 * @psalm-suppress UnresolvableInclude
 * @property-read int $cost
 */
class Weapon implements Stringable
{
    public Classification $class;
    public ?string $damage;
    public ?string $explanation;
    public string $id;
    public string $name;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $weapons;

    public function __construct(string $id)
    {
        $filename = config('transformers.data_path') . 'weapons.php';
        self::$weapons ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$weapons[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Weapon ID "%s" is invalid',
                $id
            ));
        }

        $weapon = self::$weapons[$this->id];
        $this->class = $weapon['class'];
        $this->damage = $weapon['damage'] ?? null;
        $this->explanation = $weapon['explanation'] ?? null;
        $this->name = $weapon['name'];
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __get(string $name): mixed
    {
        if ('cost' === $name) {
            return $this->cost();
        }
        return null;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function cost(): int
    {
        return match ($this->class) {
            Classification::Major => 3,
            Classification::Standard => 2,
            Classification::Minor => 1,
        };
    }
}
