<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;
use Stringable;

use function array_key_exists;
use function config;
use function sprintf;
use function strtolower;

/**
 * Expanse character's social class.
 * @psalm-suppress PossiblyUnusedProperty
 */
class SocialClass implements Stringable
{
    /**
     * Description of the social class.
     * @psalm-suppress PossiblyUnusedProperty
     */
    public string $description;

    /**
     * Name of the social class.
     */
    public string $name;

    /**
     * List of all social classes.
     * @var array<string, array<string, string>>
     */
    public static ?array $classes = null;

    /**
     * @throws RuntimeException if the ID is invalid.
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.expanse') . 'social-classes.php';
        self::$classes ??= require $filename;

        $id = strtolower($id);
        if (!array_key_exists($id, self::$classes)) {
            throw new RuntimeException(
                sprintf('Social Class ID "%s" is invalid', $id)
            );
        }

        $class = self::$classes[$id];
        $this->description = $class['description'];
        $this->id = $id;
        $this->name = $class['name'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
