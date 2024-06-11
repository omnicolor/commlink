<?php

declare(strict_types=1);

namespace App\Models\Capers;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * Skills define things your character is particularly adept at. They might be
 * things your character has studied in depth. They might be things your
 * character has a natural affinity for.
 */
class Skill implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static ?array $skills = null;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.capers') . 'skills.php';
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(
                sprintf('Skill ID "%s" is invalid', $id)
            );
        }

        $skill = self::$skills[$id];
        $this->description = $skill['description'];
        $this->name = $skill['name'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public static function all(): SkillArray
    {
        $filename = config('app.data_path.capers') . 'skills.php';
        self::$skills ??= require $filename;

        $skills = new SkillArray();
        /** @var string $id */
        foreach (array_keys(self::$skills ?? []) as $id) {
            $skills[$id] = new self($id);
        }
        return $skills;
    }
}
