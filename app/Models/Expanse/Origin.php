<?php

declare(strict_types=1);

namespace App\Models\Expanse;

/**
 * Representation of one of the origins in The Expanse.
 */
abstract class Origin
{
    /**
     * Description of the origin.
     * @var string
     */
    public string $description;

    /**
     * The name of the origin.
     * @var string
     */
    public string $name;

    /**
     * Return the origin's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Create an origin from a string.
     * @param string $id
     * @return Origin
     * @throws \RuntimeException
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
                throw new \RuntimeException(
                    \sprintf('Origin "%s" is invalid', $id)
                );
        }
    }
}
