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
 * A power that sprites can use.
 */
class SpritePower implements Stringable
{
    public readonly string $description;
    public readonly string $name;
    public readonly int $page;
    public readonly string $ruleset;

    /**
     * Collection of all potential powers.
     * @var array<string, array<string, int|string>>
     */
    public static array $powers;

    /**
     * @throws RuntimeException if the ID is not found
     */
    public function __construct(public readonly string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'sprite-powers.php';
        self::$powers = require $filename;

        $id = strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(sprintf(
                'Sprite power ID "%s" is invalid',
                $id
            ));
        }

        $power = self::$powers[$this->id];
        $this->description = $power['description'];
        $this->name = $power['name'];
        $this->page = $power['page'];
        $this->ruleset = $power['ruleset'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
