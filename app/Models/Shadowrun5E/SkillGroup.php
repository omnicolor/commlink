<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Skill group.
 */
class SkillGroup
{
    /**
     * ID of the skill group.
     * @var string
     */
    public string $id;

    /**
     * Level of the skill group.
     * @var int
     */
    public int $level;

    /**
     * Name of the skill group.
     * @var string
     */
    public string $name;

    /**
     * Skills that are part of the group.
     * @var ActiveSkill[]
     */
    public array $skills;

    /**
     * List of all skill groups.
     * @var ?array<mixed>
     */
    public static ?array $skillGroups;

    /**
     * Constructor, build the skill group object.
     * @param string $id ID of the group
     * @param int $level Level the character has for the group
     * @throws \RuntimeException If the ID is invalid
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

                if (!isset(self::$skillGroups[$skill['group']])) {
                    self::$skillGroups[$skill['group']] = [];
                }

                self::$skillGroups[$skill['group']][] =
                    new ActiveSkill($skill['id'], 0);
            }
        }

        if (!isset(self::$skillGroups[$id])) {
            throw new \RuntimeException(sprintf(
                'Skill group ID "%s" is invalid',
                $id
            ));
        }

        $this->id = $id;
        $this->name = ucfirst(str_replace('-', ' ', $id));
        $this->level = $level;
        $this->skills = self::$skillGroups[$id];
    }

    /**
     * Return the name of the skill group.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
