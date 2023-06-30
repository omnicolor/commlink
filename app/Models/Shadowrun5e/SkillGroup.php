<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Skill group.
 */
class SkillGroup
{
    /**
     * ID of the skill group.
     */
    public string $id;

    /**
     * Level of the skill group.
     */
    public int $level;

    /**
     * Name of the skill group.
     */
    public string $name;

    /**
     * Skills that are part of the group.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, ActiveSkill>
     */
    public array $skills;

    /**
     * List of all skill groups.
     * @var ?array<string, array<int, ActiveSkill>>
     */
    public static ?array $skillGroups;

    /**
     * Constructor, build the skill group object.
     * @throws RuntimeException If the ID is invalid
     */
    public function __construct(string $id, int $level)
    {
        if (!isset(self::$skillGroups)) {
            $filename = config('app.data_path.shadowrun5e') . 'skills.php';
            $skills = require $filename;

            foreach ($skills as $skill) {
                // Some skills are not in any group
                if (!isset($skill['group'])) {
                    continue;
                }

                $group = (string)$skill['group'];
                if (!isset(self::$skillGroups[$group])) {
                    self::$skillGroups[$group] = [];
                }

                self::$skillGroups[$group][] =
                    new ActiveSkill($skill['id'], 0);
            }
        }

        if (!isset(self::$skillGroups[$id])) {
            throw new RuntimeException(\sprintf(
                'Skill group ID "%s" is invalid',
                $id
            ));
        }

        $this->id = $id;
        $this->name = \ucfirst(\str_replace('-', ' ', $id));
        $this->level = $level;
        $this->skills = self::$skillGroups[$id];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
