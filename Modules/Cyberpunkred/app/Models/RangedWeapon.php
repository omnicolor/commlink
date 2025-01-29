<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

use RuntimeException;
use Stringable;

use function assert;
use function in_array;
use function sprintf;
use function strtolower;

/**
 * If something comes out of it, traverses a distance, and causes damage at the
 * end of that trajectory, it's a Ranged Weapon.
 */
class RangedWeapon extends Weapon implements Stringable
{
    /**
     * Number of rounds remaining in the magazine.
     */
    public int $ammoRemaining;

    /**
     * Number of rounds in the weapon's standard magazine.
     */
    public int $magazine;

    /**
     * Construct a new ranged weapon.
     * @param array<string, int|string> $options
     * @throws RuntimeException
     */
    protected function __construct(array $options)
    {
        $this->id = strtolower((string)$options['id']);
        assert(null !== self::$rangedWeapons);
        $weapon = self::$rangedWeapons[$this->id];
        $this->concealable = $weapon['concealable'];
        $this->cost = $weapon['cost'];
        $this->damage = $weapon['damage'];
        $this->examples = $weapon['examples'];
        $this->handsRequired = $weapon['hands-required'];
        $this->magazine = $weapon['magazine'];
        $this->rateOfFire = $weapon['rate-of-fire'];
        $this->skill = $weapon['skill'];
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
        $this->ammoRemaining = (int)($options['ammoRemaining'] ?? $this->magazine);
    }
}
