<?php

declare(strict_types=1);

namespace App\Models\Subversion;

use RuntimeException;

use function sprintf;

/**
 * @psalm-suppress UnusedClass
 * @psalm-suppress PossiblyUnusedProperty
 */
class Gear
{
    public string $category;
    public string $description;
    public ?int $firewall;
    public int $fortune;
    public string $name;
    public int $page;
    public string $ruleset;
    public ?int $security_rating;

    /**
     * @var array<string, array<string, int|string>>
     */
    public static ?array $gear;

    /**
     * @param ?array<string, int|string> $rawItem
     * @phpstan-ignore-next-line
     * @psalm-suppress PossiblyUnusedParam
     * @psalm-suppress PossiblyUnusedProperty
     */
    public function __construct(public string $id, ?array $rawItem = null)
    {
        $filename = config('app.data_path.subversion') . 'gear.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$gear ??= require $filename;

        if (!isset(self::$gear[$id])) {
            throw new RuntimeException(sprintf('Gear "%s" not found', $id));
        }

        $gear = self::$gear[$id];
        $this->category = $gear['category'];
        $this->description = $gear['description'];
        $this->firewall = $gear['firewall'] ?? null;
        $this->fortune = $gear['fortune'];
        $this->name = $gear['name'];
        $this->page = $gear['page'];
        $this->ruleset = $gear['ruleset'];
        $this->security_rating = $gear['security'] ?? null;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     * @return array<int, Gear>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.subversion') . 'gear.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$gear ??= require $filename;

        $gear = [];
        foreach (self::$gear as $item) {
            $gear[] = new Gear($item['id']);
        }
        return $gear;
    }
}
