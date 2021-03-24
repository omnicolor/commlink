<?php

declare(strict_types=1);

namespace App\Models\Expanse;

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
     * @var array<int, string>
     */
    public array $benefits;

    /**
     * Description of the Talent.
     * @var string
     */
    public string $description;

    /**
     * Unique ID for the Talent.
     * @var string
     */
    public string $id;

    /**
     * Level the character has attained in the Talent.
     * @var int
     */
    public int $level;

    /**
     * Name of the Talent.
     * @var string
     */
    public string $name;

    /**
     * Page of the rulebook for the talent.
     * @var int
     */
    public int $page;

    /**
     * Collection of requirements needed to gain the Talent after chargen.
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
     * @param string $id
     * @param int $level
     * @throws \RuntimeException
     */
    public function __construct(string $id, int $level = self::NOVICE)
    {
        $filename = config('app.data_path.expanse') . 'talents.php';
        self::$talents ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$talents[$id])) {
            throw new \RuntimeException(
                sprintf('Talent ID "%s" is invalid', $id)
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
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Set the level of the talent the character can use.
     * @param int $level
     * @throws \RuntimeException
     */
    public function setLevel(int $level): Talent
    {
        if (
            self::NOVICE !== $level
            && self::EXPERT !== $level
            && self::MASTER !== $level
        ) {
            throw new \RuntimeException('Talent level outside allowed values');
        }
        $this->level = $level;
        return $this;
    }
}
