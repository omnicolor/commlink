<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

/**
 * Base class for talents in The Expanse.
 */
class Talent
{
    public const NOVICE = 1;
    public const EXPERT = 2;
    public const MASTER = 3;
    public const JOURNEYMAN = 3;
    public const SHORT_LEVELS = [
        self::NOVICE => 'N',
        self::EXPERT => 'E',
        self::MASTER => 'M',
    ];

    /**
     * Array of descriptions of the benefits added at that level of the Talent.
     * @psalm-suppress PossiblyUnusedProperty
     * @var array<int, string>
     */
    public array $benefits;

    /**
     * Description of the Talent.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Unique ID for the Talent.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $id;

    /**
     * Level the character has attained in the Talent.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public int $level;

    /**
     * Name of the Talent.
     */
    public string $name;

    /**
     * Page of the rulebook for the talent.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public int $page;

    /**
     * Collection of requirements needed to gain the Talent after chargen.
     * @psalm-suppress PossiblyUnusedProperty
     * @var ?array<mixed>
     */
    public ?array $requirements;

    /**
     * Collection of all talents.
     * @var ?array<mixed>
     */
    public static ?array $talents;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(string $id, int $level = self::NOVICE)
    {
        $filename = config('app.data_path.expanse') . 'talents.php';
        self::$talents ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$talents[$id])) {
            throw new RuntimeException(
                \sprintf('Talent ID "%s" is invalid', $id)
            );
        }

        $talent = self::$talents[$id];
        $this->benefits = $talent['benefits'];
        $this->description = $talent['description'];
        $this->id = $id;
        $this->setLevel($level);
        $this->name = $talent['name'];
        $this->page = $talent['page'];
        $this->requirements = $talent['requirements'];
    }

    /**
     * Return the Talent's name as a string.
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Set the level of the talent the character can use.
     * @throws RuntimeException
     */
    public function setLevel(int $level): Talent
    {
        if (
            self::NOVICE !== $level
            && self::EXPERT !== $level
            && self::MASTER !== $level
        ) {
            throw new RuntimeException('Talent level outside allowed values');
        }
        $this->level = $level;
        return $this;
    }
}
