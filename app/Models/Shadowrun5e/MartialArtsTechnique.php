<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Martial arts technique.
 * @property string $id
 * @psalm-suppress PossiblyUnusedProperty
 */
class MartialArtsTechnique implements Stringable
{
    /**
     * Description of the technique.
     */
    public string $description;

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
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public string $id)
    {
        $filename = config('app.data_path.shadowrun5e')
            . 'martial-arts-techniques.php';
        self::$techniques ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$techniques[$id])) {
            throw new RuntimeException(sprintf(
                'Martial Arts Technique ID "%s" is invalid',
                $id
            ));
        }

        $technique = self::$techniques[$id];
        $this->description = $technique['description'];
        $this->name = $technique['name'];
        $this->page = $technique['page'];
        $this->ruleset = $technique['ruleset'];
        $this->subname = $technique['subname'] ?? null;
    }

    public function __toString(): string
    {
        if (null !== $this->subname) {
            return sprintf('%s (%s)', $this->name, $this->subname);
        }
        return $this->name;
    }
}
