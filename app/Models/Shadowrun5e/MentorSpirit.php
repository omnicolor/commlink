<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Mentor spirit class.
 * @psalm-suppress PossiblyUnusedProperty
 */
class MentorSpirit implements Stringable
{
    /**
     * Description of the mentor spirit.
     */
    public string $description;

    /**
     * Collection of effects the mentor spirit provides.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Name of the mentor spirit.
     */
    public string $name;

    /**
     * Page the mentor spirit was introduced on.
     */
    public ?int $page;

    /**
     * Ruleset the mentor spirit was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of all mentor spirits.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $spirits;

    /**
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'mentor-spirits.php';
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
        $filename = config('app.data_path.shadowrun5e') . 'mentor-spirits.php';
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
