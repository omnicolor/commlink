<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Base class for talents in The Expanse.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Talent implements Stringable
{
    public const int NOVICE = 1;
    public const int EXPERT = 2;
    public const int MASTER = 3;
    public const int JOURNEYMAN = 3;
    public const array SHORT_LEVELS = [
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
     * @throws RuntimeException
     */
    public function __construct(public string $id, int $level = self::NOVICE)
    {
        $filename = config('expanse.data_path') . 'talents.php';
        /** @psalm-suppress UnresolvableInclude */
        self::$talents ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$talents[$id])) {
            throw new RuntimeException(
                sprintf('Talent ID "%s" is invalid', $id)
            );
        }

        $talent = self::$talents[$id];
        $this->benefits = $talent['benefits'];
        $this->description = $talent['description'];
        $this->setLevel($level);
        $this->name = $talent['name'];
        $this->page = $talent['page'];
        $this->requirements = $talent['requirements'];
    }

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
