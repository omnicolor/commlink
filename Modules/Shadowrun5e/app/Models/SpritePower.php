<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;
use Stringable;

use function config;
use function sprintf;
use function strtolower;

/**
 * A power that sprites can use.
 * @psalm-suppress PossiblyUnusedProperty
 */
class SpritePower implements Stringable
{
    public string $description;
    public string $name;
    public int $page;
    public string $ruleset;

    /**
     * Collection of all potential powers.
     * @var array<string, array<string, int|string>>
     */
    public static array $powers;

    /**
     * @throws RuntimeException if the ID is not found
     */
    public function __construct(public string $id)
    {
        $filename = config('shadowrun5e.data_path') . 'sprite-powers.php';
        /** @psalm-suppress UnresolvableInclude */
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

    public function __toString(): string
    {
        return $this->name;
    }
}
