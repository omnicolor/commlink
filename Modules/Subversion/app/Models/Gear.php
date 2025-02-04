<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;

class Gear implements Stringable
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

    public function __construct(public string $id)
    {
        $filename = config('subversion.data_path') . 'gear.php';
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

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, Gear>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'gear.php';
        self::$gear ??= require $filename;

        $gear = [];
        foreach (self::$gear ?? [] as $item) {
            $gear[] = new Gear($item['id']);
        }
        return $gear;
    }
}
