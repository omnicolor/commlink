<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use RuntimeException;
use Stringable;

use function assert;
use function in_array;
use function sprintf;
use function strtolower;

class MeleeWeapon extends Weapon implements Stringable
{
    public ?int $ammoRemaining = null;
    public ?int $magazine = null;

    /**
     * Construct a new melee weapon.
     * @param array<string, int|string> $options
     * @throws RuntimeException
     */
    protected function __construct(array $options)
    {
        $this->id = strtolower((string)$options['id']);
        assert(null !== self::$meleeWeapons);
        $weapon = self::$meleeWeapons[$this->id];
        $this->concealable = $weapon['concealable'];
        $this->cost = $weapon['cost'];
        $this->damage = $weapon['damage'];
        $this->examples = $weapon['examples'];
        $this->handsRequired = $weapon['hands-required'];
        $this->rateOfFire = $weapon['rate-of-fire'];
        $this->skill = 'melee-weapon';
        $this->type = $weapon['type'];

        if (isset($options['quality'])) {
            if (!in_array($options['quality'], self::QUALITIES, true)) {
                throw new RuntimeException(sprintf(
                    'Weapon ID "%s" has invalid quality "%s"',
                    $this->id,
                    $options['quality']
                ));
            }
            $this->quality = $options['quality'];
        }
        $this->name = (string)($options['name'] ?? $this->type);
    }
}
