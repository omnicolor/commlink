<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

use Override;
use RuntimeException;
use Stringable;

use function sprintf;
use function strtolower;

/**
 * Representation of one of the origins in The Expanse.
 */
abstract class Origin implements Stringable
{
    /**
     * Description of the origin.
     */
    public string $description;

    /**
     * The name of the origin.
     */
    public string $name;

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Create an origin from a string.
     * @throws RuntimeException
     */
    public static function factory(string $id): Origin
    {
        $id = strtolower($id);
        return match ($id) {
            'belter' => new Belter(),
            'earther' => new Earther(),
            'martian' => new Martian(),
            default => throw new RuntimeException(
                sprintf('Origin "%s" is invalid', $id)
            ),
        };
    }
}
