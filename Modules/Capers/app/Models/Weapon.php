<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Stringable;

class Weapon extends Gear implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $damage;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $range;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $rounds;
    public string $type = 'weapon';

    public function __construct(public string $id, int $quantity)
    {
        parent::__construct($id, $quantity);

        // @phpstan-ignore-next-line
        $gear = self::$gear[$id];
        // @phpstan-ignore-next-line
        $this->damage = $gear['damage'] ?? null;
        // @phpstan-ignore-next-line
        $this->range = $gear['range'] ?? null;
        // @phpstan-ignore-next-line
        $this->rounds = $gear['rounds'] ?? null;
    }
}
