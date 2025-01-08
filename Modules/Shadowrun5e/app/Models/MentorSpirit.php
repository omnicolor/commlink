<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Mentor spirit class.
 */
final class MentorSpirit implements Stringable
{
    public readonly string $description;

    /**
     * Collection of effects the mentor spirit provides.
     * @var array<string, int>
     */
    public array $effects = [];
    public readonly string $name;
    public readonly int|null $page;
    public readonly string $ruleset;

    /**
     * Collection of all mentor spirits.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $spirits;

    /**
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'mentor-spirits.php';
        self::$spirits ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$spirits[$id])) {
            throw new RuntimeException(
                sprintf('Mentor spirit ID "%s" is invalid', $id)
            );
        }

        $spirit = self::$spirits[$id];
        $this->description = $spirit['description'];
        $this->effects = $spirit['effects'] ?? [];
        $this->name = $spirit['name'];
        $this->page = $spirit['page'] ?? null;
        $this->ruleset = $spirit['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return a mentor spirit based on its name.
     * @throws RuntimeException
     */
    public static function findByName(string $name): MentorSpirit
    {
        $filename = config('shadowrun5e.data_path') . 'mentor-spirits.php';
        self::$spirits ??= require $filename;

        foreach (self::$spirits as $id => $spirit) {
            if (strtolower((string)$spirit['name']) === strtolower($name)) {
                return new MentorSpirit($id);
            }
        }
        throw new RuntimeException(sprintf(
            'Mentor spirit name "%s" was not found',
            $name
        ));
    }
}
