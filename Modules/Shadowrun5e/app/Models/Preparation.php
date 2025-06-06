<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;

use function in_array;

/**
 * Class representing an alchemical preparation.
 */
final class Preparation
{
    public string $date;
    public int $potency;
    public Spell $spell;
    public string $trigger;

    /**
     * Set the date the preparation was created.
     */
    public function setDate(string $date): Preparation
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set the preparation's potency.
     */
    public function setPotency(int $potency): Preparation
    {
        $this->potency = $potency;
        return $this;
    }

    /**
     * Set the spell the preparation is based on.
     */
    public function setSpell(Spell $spell): Preparation
    {
        $this->spell = $spell;
        return $this;
    }

    /**
     * Set the spell the preparation is based on by its ID.
     * @throws RuntimeException if the ID is invalid
     */
    public function setSpellId(string $spell): Preparation
    {
        $this->spell = new Spell($spell);
        return $this;
    }

    /**
     * Set the trigger for the preparation.
     * @throws RuntimeException If the trigger is invalid
     */
    public function setTrigger(string $trigger): Preparation
    {
        if (!in_array($trigger, ['command', 'contact', 'time'], true)) {
            throw new RuntimeException('Invalid alchemical trigger');
        }
        $this->trigger = $trigger;
        return $this;
    }
}
