<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;

class Skill implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * @var array<int, string>
     */
    public array $attributes;

    /**
     * @var ?array<string, array<string, array<int, string>|int|string>>
     */
    public static ?array $skills;

    public function __construct(public string $id, public ?int $rank = null)
    {
        $filename = config('subversion.data_path') . 'skills.php';
        self::$skills ??= require $filename;

        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(sprintf('Skill "%s" not found', $id));
        }

        $skill = self::$skills[$id];
        $this->attributes = $skill['attributes'];
        $this->description = $skill['description'];
        $this->name = $skill['name'];
        $this->page = $skill['page'];
        $this->ruleset = $skill['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, Skill>
     */
    public static function all(): array
    {
        $filename = config('subversion.data_path') . 'skills.php';
        self::$skills ??= require $filename;

        $skills = [];
        foreach (self::$skills ?? [] as $skill) {
            $skills[(string)$skill['id']] = new Skill($skill['id']);
        }
        return $skills;
    }
}
