<?php

declare(strict_types=1);

namespace App\Models\Capers;

class Explosive extends Gear
{
    public ?string $blast;
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
