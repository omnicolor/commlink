<?php

declare(strict_types=1);

namespace App\Models\Shadowrun5e;

/**
 * Class representing an alchemical preparation.
 */
class Preparation
{
    /**
     * In-game date the preparation was created on.
     * @var string
     */
    public string $date;

    /**
     * Potency of the preparation.
     * @var int
     */
    public int $potency;

    /**
     * Spell the preparation was created from.
     * @var Spell
     */
    public Spell $spell;

    /**
     * Trigger for the preparation.
     * @var string
     */
    public string $trigger;

    /**
     * Set the date the preparation was created.
     * @param string $date
     * @return Preparation
     */
    public function setDate(string $date): Preparation
    {
        $this->date = $date;
        return $this;
    }

    /**
     * Set the preparation's potency.
     * @param int $potency
     * @return Preparation
     */
    public function setPotency(int $potency): Preparation
    {
        $this->potency = $potency;
        return $this;
    }

    /**
     * Set the spell the preparation is based on.
     * @param Spell $spell
     * @return Preparation
     */
    public function setSpell(Spell $spell): Preparation
    {
        $this->spell = $spell;
        return $this;
    }

    /**
     * Set the spell the preparation is based on by its ID.
     * @param string $spell
     * @return Preparation
     * @throws \RuntimeException if the ID is invalid
     */
    public function setSpellId(string $spell): Preparation
    {
        $this->spell = new Spell($spell);
        return $this;
    }

    /**
     * Set the trigger for the preparation.
     * @param string $trigger Trigger to give the preparation
     * @return Preparation
     * @throws \RuntimeException If the trigger is invalid
     */
    public function setTrigger(string $trigger): Preparation
    {
        if (!\in_array($trigger, ['command', 'contact', 'time'], true)) {
            throw new \RuntimeException('Invalid alchemical trigger');
        }
        $this->trigger = $trigger;
        return $this;
    }
}
