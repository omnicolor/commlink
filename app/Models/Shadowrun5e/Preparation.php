<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

use RuntimeException;

/**
 * Class representing an alchemical preparation.
 * @psalm-suppress PossiblyUnusedProperty
 */
class Preparation
{
    /**
     * In-game date the preparation was created on.
     */
    public string $date;

    /**
     * Potency of the preparation.
     */
    public int $potency;

    /**
     * Spell the preparation was created from.
     */
    public Spell $spell;

    /**
     * Trigger for the preparation.
     */
    public string $trigger;

    /**
     * Set the date the preparation was created.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setDate(string $date): Preparation
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set the preparation's potency.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setPotency(int $potency): Preparation
    {
        $this->potency = $potency;
        return $this;
    }

    /**
     * Set the spell the preparation is based on.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function setSpell(Spell $spell): Preparation
    {
        $this->spell = $spell;
        return $this;
    }

    /**
     * Set the spell the preparation is based on by its ID.
     * @psalm-suppress PossiblyUnusedMethod
     * @throws RuntimeException if the ID is invalid
     */
    public function setSpellId(string $spell): Preparation
    {
        $this->spell = new Spell($spell);
        return $this;
    }

    /**
     * Set the trigger for the preparation.
     * @psalm-suppress PossiblyUnusedMethod
     * @throws RuntimeException If the trigger is invalid
     */
    public function setTrigger(string $trigger): Preparation
    {
        if (!\in_array($trigger, ['command', 'contact', 'time'], true)) {
            throw new RuntimeException('Invalid alchemical trigger');
        }
        $this->trigger = $trigger;
        return $this;
    }
}
