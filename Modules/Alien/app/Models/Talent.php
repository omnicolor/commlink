<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Talent implements Stringable
{
    public ?string $career;
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $talents = null;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'talents.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$talents ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$talents[$id])) {
            throw new RuntimeException(sprintf(
                'Talent ID "%s" is invalid',
                $id
            ));
        }

        $talent = self::$talents[$id];
        $this->career = $talent['career'];
        $this->description = $talent['description'];
        $this->name = $talent['name'];
        $this->page = $talent['page'];
        $this->ruleset = $talent['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<int, self>
     */
    public static function all(): array
    {
        $filename = config('alien.data_path') . 'talents.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$talents ??= require $filename;

        $talents = [];
        /** @var string $id */
        foreach (array_keys(self::$talents) as $id) {
            $talents[] = new self($id);
        }
        return $talents;
    }
}
