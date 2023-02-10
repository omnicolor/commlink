<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Skill a character can use.
 */
class ActiveSkill extends Skill
{
    /**
     * Whether the character can default this skill.
     * @var bool
     */
    public bool $default = false;

    /**
     * Description of the skill.
     * @var string
     */
    public string $description;

    /**
     * Skill group the skill belongs to.
     * @var ?string
     */
    public ?string $group = null;

    /**
     * ID of the skill.
     * @var string
     */
    public string $id;

    /**
     * List of all skills.
     * @var ?array<mixed>
     */
    public static ?array $skills;

    /**
     * Construct a skill for the character.
     * @param string $id ID of the skill to load
     * @param int $level Level the character has for the skill
     * @param string $specialization Optional specialization
     * @throws \RuntimeException If the skill isn't valid
     */
    public function __construct(string $id, int $level, $specialization = null)
    {
        $filename = config('app.data_path.shadowrun5e') . 'skills.php';
        self::$skills ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new \RuntimeException(\sprintf(
                'Skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->default = $skill['default'] ?? false;
        $this->description = $skill['description'];
        $this->group = $skill['group'] ?? null;
        $this->id = $id;
        $this->level = $level;
        $this->limit = $skill['limit'] ?? '?';
        $this->name = $skill['name'];
        $this->specialization = $specialization;
    }

    /**
     * Try to find a skill's ID based on its name.
     * @param string $name
     * @return string
     * @throws \RuntimeException
     */
    public static function findIdByName(string $name): string
    {
        $filename = config('app.data_path.shadowrun5e') . 'skills.php';
        self::$skills ??= require $filename;
        foreach (self::$skills as $skill) {
            if ($skill['name'] === $name) {
                return $skill['id'];
            }
        }
        throw new \RuntimeException(\sprintf(
            'Active skill "%s" not found',
            $name
        ));
    }

    /**
     * Return all available active skills.
     * @return SkillArray
     */
    public static function all(): SkillArray
    {
        $filename = config('app.data_path.shadowrun5e') . 'skills.php';
        self::$skills ??= require $filename;

        $skills = new SkillArray();
        /** @var string $id */
        foreach (array_keys(self::$skills) as $id) {
            $skills[] = new self($id, 1);
        }
        return $skills;
    }
}
