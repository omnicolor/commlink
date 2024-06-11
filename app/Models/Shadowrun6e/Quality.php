<?php

declare(strict_types=1);

namespace App\Models\Shadowrun6e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class Quality implements Stringable
{
    public string $description;
    public int $karma_cost;
    public ?int $level;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<string, mixed>
     */
    public array $effects;

    /**
     * Collection of all qualities.
     * @var array<string, array<string, array<string, mixed>|int|string>>
     */
    public static array $qualities;

    /**
     * @psalm-suppress UnusedVariable
     * @throws RuntimeException
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun6e') . 'qualities.php';
        self::$qualities ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$qualities[$this->id])) {
            throw new RuntimeException(sprintf(
                'Shadowrun 6E quality ID "%s" is invalid',
                $this->id
            ));
        }

        $quality = self::$qualities[$this->id];
        $this->description = $quality['description'];
        $this->effects = $quality['effects'] ?? [];
        $this->karma_cost = $quality['karma_cost'];
        $this->level = $quality['level'] ?? null;
        $this->name = $quality['name'];
        $this->page = $quality['page'];
        $this->ruleset = $quality['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Try to find a quality by its name.
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Quality>
     * @throws RuntimeException
     */
    public static function findByName(string $name): array
    {
        $filename = config('app.data_path.shadowrun6e') . 'qualities.php';
        self::$qualities ??= require $filename;

        $qualities = [];
        foreach (self::$qualities as $id => $quality) {
            if (strtolower((string)$quality['name']) === strtolower($name)) {
                $qualities[] = new Quality($id);
            }
        }
        if (0 === count($qualities)) {
            throw new RuntimeException(sprintf(
                'Unable to find Shadowrun 6E quality "%s"',
                $name
            ));
        }
        return $qualities;
    }
}
