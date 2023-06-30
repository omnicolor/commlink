<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

/**
 * Representation of one of the origins in The Expanse.
 */
abstract class Origin
{
    /**
     * Description of the origin.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * The name of the origin.
     */
    public string $name;

    /**
     * Return the origin's name.
     */
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
        $id = \strtolower($id);
        switch ($id) {
            case 'belter':
                return new Origin\Belter();
            case 'earther':
                return new Origin\Earther();
            case 'martian':
                return new Origin\Martian();
            default:
                throw new RuntimeException(
                    \sprintf('Origin "%s" is invalid', $id)
                );
        }
    }
}
