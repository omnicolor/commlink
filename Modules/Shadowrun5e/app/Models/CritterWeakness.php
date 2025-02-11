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
 * Representation of a critter's weakness.
 */
final class CritterWeakness implements Stringable
{
    public readonly string $description;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Collection of all weaknesses.
     * @var ?array<string, array<string, int|string>>
     */
    public static ?array $weaknesses;

    /**
     * @param ?string $subname Optional additional name of the weakness
     * @throws RuntimeException
     */
    public function __construct(public readonly string $id, public ?string $subname = null)
    {
        $filename = config('shadowrun5e.data_path')
            . 'critter-weaknesses.php';
        self::$weaknesses ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$weaknesses[$id])) {
            throw new RuntimeException(sprintf(
                'Critter weakness "%s" is invalid',
                $id
            ));
        }

        $weakness = self::$weaknesses[$this->id];
        $this->description = $weakness['description'];
        $this->name = $weakness['name'];
        $this->page = $weakness['page'];
        $this->ruleset = $weakness['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        if (null !== $this->subname) {
            return sprintf('%s - %s', $this->name, $this->subname);
        }
        return $this->name;
    }
}
