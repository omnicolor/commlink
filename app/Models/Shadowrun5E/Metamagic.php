<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5E;

/**
 * Class representing a metamagic in Shadowrun.
 */
class Metamagic
{
    /**
     * Whether the metamagic is for adepts only.
     * @var bool
     */
    public bool $adeptOnly;

    /**
     * Description of the metamagic.
     * @var string
     */
    public string $description;

    /**
     * Name of the metamagic.
     * @var string
     */
    public string $name;

    /**
     * Page the metamagic was introduced on.
     * @var int
     */
    public int $page;

    /**
     * Ruleset the metamagic was introduced in.
     * @var string
     */
    public string $ruleset;

    /**
     * Collection of all metamagics.
     * @var array<string, array<string, bool|int|string>>
     */
    public static ?array $metamagics;

    /**
     * Constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'metamagics.php';
        self::$metamagics ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$metamagics[$id])) {
            throw new \RuntimeException(
                \sprintf('Metamagic ID "%s" is invalid', $id)
            );
        }

        $magic = self::$metamagics[$id];
        $this->adeptOnly = (bool)$magic['adeptOnly'];
        $this->description = (string)$magic['description'];
        $this->name = (string)$magic['name'];
        $this->page = (int)$magic['page'];
        $this->ruleset = (string)$magic['ruleset'];
    }

    /**
     * Return the metamagic as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Find a metamagic by its name instead of ID.
     * @param string $name
     * @return Metamagic
     * @throws \RuntimeException
     */
    public static function findByName(string $name): Metamagic
    {
        $filename = config('app.data_path.shadowrun5e') . 'metamagics.php';
        self::$metamagics ??= require $filename;

        foreach (self::$metamagics as $meta) {
            if ($meta['name'] === $name) {
                return new self($meta['id']);
            }
        }
        throw new \RuntimeException(
            \sprintf('Metamagic "%s" was not found', $name)
        );
    }
}
