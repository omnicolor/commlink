<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Representation of a critter's weakness.
 */
class CritterWeakness
{
    /**
     * Description of the weakness.
     * @var string
     */
    public string $description;

    /**
     * Unique ID for the weakness.
     * @var string
     */
    public string $id;

    /**
     * Name of the weakness.
     * @var string
     */
    public string $name;

    /**
     * Page the weakness is described on.
     * @var int
     */
    public int $page;

    /**
     * Ruleset the weakness is introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all weaknesses.
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $weaknesses;

    /**
     * Construct.
     * @param string $id
     * @param ?string $subname Optional additional name of the weakness
     * @throws RuntimeException
     */
    public function __construct(string $id, public ?string $subname = null)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'critter-weaknesses.php';
        self::$weaknesses ??= require $filename;

        $this->id = \strtolower($id);
        if (!isset(self::$weaknesses[$this->id])) {
            throw new RuntimeException(\sprintf(
                'Critter weakness "%s" is invalid',
                $this->id
            ));
        }

        $weakness = self::$weaknesses[$this->id];
        $this->description = $weakness['description'];
        $this->name = $weakness['name'];
        $this->page = $weakness['page'];
        $this->ruleset = $weakness['ruleset'];
    }

    public function __toString(): string
    {
        if (null !== $this->subname) {
            return \sprintf('%s - %s', $this->name, $this->subname);
        }
        return $this->name;
    }
}
