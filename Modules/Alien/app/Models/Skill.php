<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Skill implements Stringable
{
    public string $attribute;
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;
    /** @var array<int, string> */
    public array $stunts;

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $skills = null;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(sprintf(
                'Skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->description = $skill['description'];
        $this->name = $skill['name'];
        $this->page = $skill['page'];
        $this->ruleset = $skill['ruleset'];
        $this->stunts = $skill['stunts'];
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
        $filename = config('alien.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $skills = [];
        /** @var string $id */
        foreach (array_keys(self::$skills) as $id) {
            $skills[] = new self($id);
        }
        return $skills;
    }
}
