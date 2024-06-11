<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

use RuntimeException;
use Stringable;

use function sprintf;

/**
 * Class representing a Talent.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Talent implements Stringable
{
    /**
     * Description of the talent.
     */
    public string $description;

    /**
     * IDs of talents incompatible with this one.
     * @var array<int, string>
     */
    public array $incompatibleWith;

    /**
     * Name of the talent.
     */
    public string $name;

    /**
     * Page the talent was mentioned on.
     */
    public int $page;

    /**
     * Conditions required to add this talent to a character.
     * @var array<string, int|string>
     */
    public array $requirements;

    /**
     * Book the talent was mentioned in.
     */
    public string $ruleset;

    /**
     * List of all talents.
     * @var array<string, array<string, int|string>>
     */
    public static ?array $talents;

    public function __construct(public string $id, public ?string $extra = null)
    {
        $filename = config('app.data_path.star-trek-adventures')
            . 'talents.php';
        self::$talents ??= require $filename;

        if (!isset(self::$talents[$id])) {
            throw new RuntimeException(
                sprintf('Talent ID "%s" is invalid', $id)
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
     */
    public function __toString(): string
    {
        if (null === $this->extra) {
            return $this->name;
        }
        return sprintf('%s %s', $this->name, $this->extra);
    }
}
