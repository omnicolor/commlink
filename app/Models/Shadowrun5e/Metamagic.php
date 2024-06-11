<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Class representing a metamagic in Shadowrun.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Metamagic implements Stringable
{
    /**
     * Whether the metamagic is for adepts only.
     */
    public bool $adeptOnly;

    /**
     * Description of the metamagic.
     */
    public string $description;

    /**
     * Name of the metamagic.
     */
    public string $name;

    /**
     * Page the metamagic was introduced on.
     */
    public int $page;

    /**
     * Ruleset the metamagic was introduced in.
     */
    public string $ruleset;

    /**
     * Collection of all metamagics.
     * @var array<string, array<string, bool|int|string>>
     */
    public static ?array $metamagics;

    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'metamagics.php';
        self::$metamagics ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$metamagics[$id])) {
            throw new RuntimeException(
                sprintf('Metamagic ID "%s" is invalid', $id)
            );
        }

        $magic = self::$metamagics[$id];
        $this->adeptOnly = (bool)$magic['adeptOnly'];
        $this->description = (string)$magic['description'];
        $this->name = (string)$magic['name'];
        $this->page = (int)$magic['page'];
        $this->ruleset = (string)$magic['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Find a metamagic by its name instead of ID.
     * @throws RuntimeException
     */
    public static function findByName(string $name): Metamagic
    {
        $filename = config('app.data_path.shadowrun5e') . 'metamagics.php';
        self::$metamagics ??= require $filename;

        $name = strtolower($name);
        foreach (self::$metamagics as $meta) {
            if (strtolower((string)$meta['name']) === $name) {
                return new self($meta['id']);
            }
        }
        throw new RuntimeException(
            sprintf('Metamagic "%s" was not found', $name)
        );
    }
}
