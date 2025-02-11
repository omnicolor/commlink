<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

final class MartialArtsTechnique implements Stringable
{
    public readonly string $description;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Optional subname for the technique.
     */
    public readonly null|string $subname;

    /**
     * Collection of techniques.
     * @var ?array<string, array<string, mixed>>
     */
    public static ?array $techniques;

    /**
     * @throws RuntimeException if the ID is invalid
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path')
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

    #[Override]
    public function __toString(): string
    {
        if (null !== $this->subname) {
            return sprintf('%s (%s)', $this->name, $this->subname);
        }
        return $this->name;
    }
}
