<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * Skill a character can use.
 */
class ActiveSkill extends Skill implements Stringable
{
    /**
     * Whether the character can default this skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public bool $default = false;

    /**
     * Description of the skill.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Skill group the skill belongs to.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public ?string $group = null;

    /**
     * List of all skills.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $skills;

    /**
     * Construct a skill for the character.
     * @throws RuntimeException If the skill isn't valid
     */
    public function __construct(
        public string $id,
        int $level,
        ?string $specialization = null
    ) {
        $filename = config('shadowrun5e.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$skills[$id])) {
            throw new RuntimeException(\sprintf(
                'Skill ID "%s" is invalid',
                $id
            ));
        }

        $skill = self::$skills[$id];
        $this->attribute = $skill['attribute'];
        $this->default = $skill['default'] ?? false;
        $this->description = $skill['description'];
        $this->group = $skill['group'] ?? null;
        $this->level = $level;
        $this->limit = $skill['limit'] ?? '?';
        $this->name = $skill['name'];
        $this->specialization = $specialization;
    }

    /**
     * Try to find a skill's ID based on its name.
     * @throws RuntimeException
     */
    public static function findIdByName(string $name): string
    {
        $filename = config('shadowrun5e.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;
        foreach (self::$skills as $skill) {
            if ($skill['name'] === $name) {
                return $skill['id'];
            }
        }
        throw new RuntimeException(sprintf(
            'Active skill "%s" not found',
            $name
        ));
    }

    /**
     * Return all available active skills.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public static function all(): SkillArray
    {
        $filename = config('shadowrun5e.data_path') . 'skills.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$skills ??= require $filename;

        $skills = new SkillArray();
        /** @var string $id */
        foreach (array_keys(self::$skills) as $id) {
            $skills[] = new self($id, 1);
        }
        return $skills;
    }
}
