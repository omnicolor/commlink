<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Martial arts technique.
 * @psalm-suppress PossiblyUnusedProperty
 */
class MartialArtsTechnique
{
    /**
     * Description of the technique.
     */
    public string $description;

    /**
     * Unique ID for the technique.
     */
    public string $id;

    /**
     * Name of the technique.
     */
    public string $name;

    /**
     * Page the technique was introduced on.
     */
    public int $page;

    /**
     * Rulebook technique was introduced in.
     */
    public string $ruleset;

    /**
     * Optional subname for the technique.
     */
    public ?string $subname;

    /**
     * Collection of techniques.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $techniques;

    /**
     * Construct a new Technique object.
     * @param string $id ID to load
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'martial-arts-techniques.php';
        self::$techniques ??= require $filename;

        $id = \strtolower($id);
        if (!isset(self::$techniques[$id])) {
            throw new RuntimeException(\sprintf(
                'Martial Arts Technique ID "%s" is invalid',
                $id
            ));
        }

        $technique = self::$techniques[$id];
        $this->description = $technique['description'];
        $this->id = $id;
        $this->name = $technique['name'];
        $this->page = $technique['page'];
        $this->ruleset = $technique['ruleset'];
        $this->subname = $technique['subname'] ?? null;
    }

    /**
     * Returns the name of the technique.
     * @return string
     */
    public function __toString(): string
    {
        if (null !== $this->subname) {
            return \sprintf('%s (%s)', $this->name, $this->subname);
        }
        return $this->name;
    }
}
