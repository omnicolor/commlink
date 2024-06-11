<?php

declare(strict_types=1);

namespace App\Models\Expanse;

use RuntimeException;
use Stringable;

use function array_keys;
use function config;
use function sprintf;
use function strtolower;

/**
 * Class representing an Expanse Focus.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Focus implements Stringable
{
    /**
     * Attributes the Focus is attached to.
     */
    public string $attribute;

    /**
     * Description of the Focus.
     */
    public string $description;

    /**
     * Collection of all focuses.
     * @var ?array<string, array<string, string|int>>
     */
    public static ?array $focuses = null;

    /**
     * Name of the Focus.
     */
    public string $name;

    /**
     * Page the focus is listed on.
     */
    public int $page;

    /**
     * Constructor.
     * @throws RuntimeException
     */
    public function __construct(public string $id, public int $level = 1)
    {
        $filename = config('app.data_path.expanse') . 'focuses.php';
        self::$focuses ??= require $filename;

        $id = strtolower($id);
        if (!isset(self::$focuses[$id])) {
            throw new RuntimeException(
                sprintf('Focus ID "%s" is invalid', $id)
            );
        }

        $focus = self::$focuses[$id];
        $this->attribute = $focus['attribute'];
        $this->description = $focus['description'];
        $this->name = $focus['name'];
        $this->page = $focus['page'];
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, Focus>
     */
    public static function all(): array
    {
        $filename = config('app.data_path.expanse') . 'focuses.php';
        self::$focuses ??= require $filename;

        $focuses = [];
        /** @var string $focus */
        foreach (array_keys(self::$focuses) as $focus) {
            $focuses[$focus] = new self($focus);
        }
        return $focuses;
    }
}
