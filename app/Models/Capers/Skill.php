<?php

declare(strict_types=1);

namespace App\Models\Capers;

use RuntimeException;

/**
 * Skills define things your character is particularly adept at. They might be
 * things your character has studied in depth. They might be things your
 * character has a natural affinity for.
 */
class Skill
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    /** @psalm-suppress PossiblyUnusedMethod */
    public string $id;
    public string $name;

    /**
     * @var array<string, array<string, string>>
     */
    public static ?array $skills;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.capers') . 'skills.php';
        self::$skills ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$skills[$this->id])) {
            throw new RuntimeException(
                \sprintf('Skill ID "%s" is invalid', $id)
            );
        }

        $skill = self::$skills[$this->id];
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
