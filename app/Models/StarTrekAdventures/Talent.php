<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use RuntimeException;

/**
 * Class representing a Talent.
 */
class Talent
{
    /**
     * Description of the talent.
     * @var string
     */
    public string $description;

    /**
     * IDs of talents incompatible with this one.
     * @var array<int, string>
     */
    public array $incompatibleWith;

    /**
     * Name of the talent.
     * @var string
     */
    public string $name;

    /**
     * Page the talent was mentioned on.
     * @var int
     */
    public int $page;

    /**
     * Conditions required to add this talent to a character.
     * @var array<string, int|string>
     */
    public array $requirements;

    /**
     * Book the talent was mentioned in.
     * @var string
     */
    public string $ruleset;

    /**
     * List of all talents.
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $talents;

    public function __construct(public string $id, public ?string $extra = null)
    {
        $filename = config('app.data_path.star-trek-adventures')
            . 'talents.php';
        self::$talents ??= require $filename;

        if (!isset(self::$talents[$id])) {
            throw new RuntimeException(
                \sprintf('Talent ID "%s" is invalid', $id)
            );
        }

        $talent = self::$talents[$id];
        $this->description = $talent['description'];
        $this->incompatibleWith = $talent['incompatible-with'] ?? [];
        $this->name = $talent['name'];
        $this->page = (int)$talent['page'];
        $this->requirements = $talent['requirements'] ?? [];
        $this->ruleset = $talent['ruleset'];
    }

    /**
     * Return the talent's name.
     * @return string
     */
    public function __toString(): string
    {
        if (null === $this->extra) {
            return $this->name;
        }
        return \sprintf('%s %s', $this->name, $this->extra);
    }
}
