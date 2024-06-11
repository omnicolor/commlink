<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

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
        $filename = config('app.data_path.shadowrun5e') . 'sprite-powers.php';
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
