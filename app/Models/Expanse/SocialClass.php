<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;

/**
 * Expanse character's social class.
 */
class SocialClass
{
    /**
     * Description of the social class.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * ID of the social class.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $id;

    /**
     * Name of the social class.
     */
    public string $name;

    /**
     * List of all social classes.
     * @var array<string, array<string, string>>
     */
    public static ?array $classes;

    /**
     * Constructor.
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.expanse') . 'social-classes.php';
        self::$classes ??= require $filename;

        $id = \strtolower($id);
        if (!\array_key_exists($id, self::$classes)) {
            throw new RuntimeException(
                \sprintf('Social Class ID "%s" is invalid', $id)
            );
        }

        $class = self::$classes[$id];
        $this->description = $class['description'];
        $this->id = $id;
        $this->name = $class['name'];
    }

    /**
     * Return the name of the social class.
     */
    public function __toString(): string
    {
        return $this->name;
    }
}
