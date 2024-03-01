<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

use function sprintf;
use function strtolower;

/**
 * Shadowrun 5E echoes: powers a technomancer can take when they submerge.
 * @psalm-suppress UnusedClass
 */
class ResonanceEcho
{
    public string $description;

    /**
     * @var array<string, int|string>
     */
    public array $effects;

    /**
     * Number of times the echo can be taken after submerging.
     */
    public int $limit;

    public string $name;

    public string $id;

    public int $page;

    public string $ruleset;

    /**
     * @var ?array<string, array<string, array<string, int|string>|int|string>>
     */
    public static ?array $echoes;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.shadowrun5e') . 'resonance-echoes.php';
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
        $this->id = $id;
        $this->limit = $echo['limit'];
        $this->name = $echo['name'];
        $this->page = $echo['page'] ?? null;
        $this->ruleset = $echo['ruleset'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
