<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Mentor spirit class.
 */
class MentorSpirit
{
    /**
     * Description of the mentor spirit.
     * @var string
     */
    public string $description;

    /**
     * Collection of effects the mentor spirit provides.
     * @var array<string, int>
     */
    public array $effects = [];

    /**
     * Unique ID for the mentor spirit.
     * @var string
     */
    public string $id;

    /**
     * Name of the mentor spirit.
     * @var string
     */
    public string $name;

    /**
     * Page the mentor spirit was introduced on.
     * @var ?int
     */
    public ?int $page;

    /**
     * Ruleset the mentor spirit was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all mentor spirits.
     * @var ?array<mixed>
     */
    public static ?array $spirits;

    /**
     * Constructor.
     * @param string $id ID to load
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'mentor-spirits.php';
        self::$spirits ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$spirits[$id])) {
            throw new RuntimeException(
                \sprintf('Mentor spirit ID "%s" is invalid', $id)
            );
        }

        $spirit = self::$spirits[$id];
        $this->description = $spirit['description'];
        $this->effects = $spirit['effects'] ?? [];
        $this->id = $id;
        $this->name = $spirit['name'];
        $this->page = $spirit['page'] ?? null;
        $this->ruleset = $spirit['ruleset'];
    }

    /**
     * Return the name of the mentor spirit.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
