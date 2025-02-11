<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function str_replace;
use function ucfirst;

/**
 * Skill group.
 */
final class SkillGroup implements Stringable
{
    public readonly string $name;

    /**
     * Skills that are part of the group.
     * @var array<int, ActiveSkill>
     */
    public array $skills;

    /**
     * List of all skill groups.
     * @var ?array<string, array<int, ActiveSkill>>
     */
    public static ?array $skillGroups;

    /**
     * @throws RuntimeException If the ID is invalid
     */
    public function __construct(public readonly string $id, public int $level)
    {
        if (!isset(self::$skillGroups)) {
            $filename = config('shadowrun5e.data_path') . 'skills.php';
            $skills = require $filename;

            foreach ($skills as $skill) {
                // Some skills are not in any group.
                if (!isset($skill['group'])) {
                    continue;
                }

                $group = (string)$skill['group'];
                if (!isset(self::$skillGroups[$group])) {
                    self::$skillGroups[$group] = [];
                }

                self::$skillGroups[$group][] = new ActiveSkill($skill['id'], 0);
            }
        }

        if (!isset(self::$skillGroups[$id])) {
            throw new RuntimeException(sprintf(
                'Skill group ID "%s" is invalid',
                $id
            ));
        }

        $this->name = ucfirst(str_replace('-', ' ', $id));
        $this->skills = self::$skillGroups[$id];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
