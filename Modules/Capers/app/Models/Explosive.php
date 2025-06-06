<?php

declare(strict_types=1);

namespace Modules\Capers\Models;

use Override;
use Stringable;

use function array_key_exists;
use function assert;
use function is_array;

class Explosive extends Gear implements Stringable
{
    public ?string $blast;
    public ?string $damage;
    public string $type = 'explosive';

    public function __construct(public string $id, int $quantity)
    {
        parent::__construct($id, $quantity);

        // Invalid item handled by parent.
        assert(is_array(self::$gear));
        assert(array_key_exists($id, self::$gear));
        $gear = self::$gear[$id];
        $this->blast = (string)$gear['blast'];
        $this->damage = (string)$gear['damage'];
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
