<?php

declare(strict_types=1);

namespace App\Models\Cyberpunkred;

use RuntimeException;
use Stringable;

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
     * @psalm-suppress PossiblyUnusedProperty
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
        $id = strtolower((string)$options['id']);
        // @phpstan-ignore-next-line
        $weapon = self::$rangedWeapons[$id];
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
                    $id,
                    $options['quality']
                ));
            }
            $this->quality = $options['quality'];
        }
        $this->name = $options['name'] ?? $this->type;
        $this->ammoRemaining = $options['ammoRemaining'] ?? $this->magazine;
    }
}
