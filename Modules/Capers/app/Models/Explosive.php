<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Stringable;

class Explosive extends Gear implements Stringable
{
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $blast;
    /** @psalm-suppress PossiblyUnusedProperty */
    public ?string $damage;
    public string $type = 'explosive';

    public function __construct(public string $id, int $quantity)
    {
        parent::__construct($id, $quantity);

        // @phpstan-ignore-next-line
        $gear = self::$gear[$id];
        // @phpstan-ignore-next-line
        $this->blast = $gear['blast'];
        // @phpstan-ignore-next-line
        $this->damage = $gear['damage'];
    }
}
