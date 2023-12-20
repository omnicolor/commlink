<?php

declare(strict_types=1);

namespace App\Models\Stillfleet;

use RuntimeException;

use function sprintf;
use function strtolower;

/**
 * A power granted to a character by their class or species, or achieved through
 * leveling up.
 */
class Power
{
    public const TYPE_ADVANCED = 'advanced';
    public const TYPE_CLASS = 'class';
    public const TYPE_HELL_SCIENCE = 'hell-science';
    public const TYPE_MARQUEE = 'marquee';
    public const TYPE_SPECIES = 'species';

    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $advanced_list;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $description;
    public string $id;
    public string $name;
    /** @psalm-suppress PossiblyUnusedProperty */
    public int $page;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $ruleset;
    /** @psalm-suppress PossiblyUnusedProperty */
    public string $type;

    /** @var ?array<string, array<string, int|string>> */
    protected static ?array $powers;

    public function __construct(string $id)
    {
        $filename = config('app.data_path.stillfleet') . 'powers.php';
        self::$powers ??= require $filename;

        $this->id = strtolower($id);
        if (!isset(self::$powers[$this->id])) {
            throw new RuntimeException(sprintf(
                'Power ID "%s" is invalid',
                $id
            ));
        }

        $power = self::$powers[$this->id];
        $this->advanced_list = $power['advanced-list'] ?? null;
        $this->description = $power['description'];
        $this->name = $power['name'];
        $this->page = $power['page'];
        $this->ruleset = $power['ruleset'];
        $this->type = $power['type'];
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
