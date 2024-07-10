<?php

declare(strict_types=1);

namespace Modules\Alien\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

class Career implements Stringable
{
    public string $description;
    public string $keyAttribute;
    /** @var array<int, Skill> */
    public array $keySkills = [];
    public string $name;
    public int $page;
    public string $ruleset;
    /** @var array <int, Talent> */
    public array $talents = [];

    /**
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $careers = null;

    public function __construct(public string $id)
    {
        $filename = config('alien.data_path') . 'careers.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$careers ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$careers[$id])) {
            throw new RuntimeException(sprintf(
                'Career ID "%s" is invalid',
                $id
            ));
        }

        $career = self::$careers[$id];
        $this->description = $career['description'];
        $this->keyAttribute = $career['key-attribute'];
        foreach ($career['key-skills'] as $skill) {
            $this->keySkills[] = new Skill($skill);
        }
        $this->name = $career['name'];
        $this->page = $career['page'];
        $this->ruleset = $career['ruleset'];
        foreach ($career['talents'] as $talent) {
            try {
                $this->talents[] = new Talent($talent);
            } catch (RuntimeException) {
                // Ignore
            }
        }
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
        $filename = config('alien.data_path') . 'careers.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$careers ??= require $filename;

        $careers = [];
        /** @var string $id */
        foreach (array_keys(self::$careers) as $id) {
            $careers[] = new self($id);
        }
        return $careers;
    }
}
