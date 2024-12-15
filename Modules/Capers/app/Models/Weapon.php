<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Stringable;

use function array_key_exists;
use function assert;
use function is_array;

class Weapon extends Gear implements Stringable
{
    public ?string $damage = null;
    public ?string $range = null;
    public ?string $rounds = null;
    public string $type = 'weapon';

    public function __construct(public string $id, int $quantity)
    {
        parent::__construct($id, $quantity);

        // Invalid item handled by parent.
        assert(is_array(self::$gear));
        assert(array_key_exists($id, self::$gear));
        $gear = self::$gear[$id];
        if (isset($gear['damage'])) {
            $this->damage = (string)$gear['damage'];
        }
        if (isset($gear['range'])) {
            $this->range = (string)$gear['range'];
        }
        if (isset($gear['rounds'])) {
            $this->rounds = (string)$gear['rounds'];
        }
    }
}
