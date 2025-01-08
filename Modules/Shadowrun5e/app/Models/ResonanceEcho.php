<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * Shadowrun 5E echoes: powers a technomancer can take when they submerge.
 */
final class ResonanceEcho implements Stringable
{
    public readonly string $description;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * @var array<string, int|string>
     */
    public array $effects;

    /**
     * Number of times the echo can be taken after submerging.
     */
    public readonly int $limit;

    /**
     * @var ?array<string, array<string, array<string, int|string>|int|string>>
     */
    public static ?array $echoes;

    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'resonance-echoes.php';
        self::$echoes ??= require $filename;
        $id = strtolower($id);
        if (!isset(self::$echoes[$id])) {
            throw new RuntimeException(sprintf(
                'Echo ID "%s" is invalid',
                $id
            ));
        }

        $echo = self::$echoes[$id];
        $this->description = $echo['description'];
        $this->effects = $echo['effects'] ?? [];
        $this->limit = $echo['limit'];
        $this->name = $echo['name'];
        $this->page = $echo['page'] ?? null;
        $this->ruleset = $echo['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
