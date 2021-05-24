<?php

declare(strict_types=1);

namespace App\Models\CyberpunkRed;

class MeleeWeapon extends Weapon
{
    /**
     * Construct a new melee weapon.
     * @param array<string, int|string> $options
     * @throws \RuntimeException
     */
    protected function __construct(array $options)
    {
        $id = \strtolower((string)$options['id']);
        $weapon = self::$meleeWeapons[$id];
        $this->concealable = $weapon['concealable'];
        $this->cost = $weapon['cost'];
        $this->damage = $weapon['damage'];
        $this->examples = $weapon['examples'];
        $this->handsRequired = $weapon['hands-required'];
        $this->rateOfFire = $weapon['rate-of-fire'];
        $this->skill = 'melee-weapon';
        $this->type = $weapon['type'];

        if (isset($options['quality'])) {
            if (!\in_array($options['quality'], self::QUALITIES, true)) {
                throw new \RuntimeException(\sprintf(
                    'Weapon ID "%s" has invalid quality "%s"',
                    $id,
                    $options['quality']
                ));
            }
            $this->quality = $options['quality'];
        }
        $this->name = $options['name'] ?? $this->type;
    }
}
