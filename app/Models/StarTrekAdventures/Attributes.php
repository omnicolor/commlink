<?php

declare(strict_types=1);

namespace App\Models\StarTrekAdventures;

/**
 * Character attributes.
 */
class Attributes
{
    public int $control;
    public int $daring;
    public int $fitness;
    public int $insight;
    public int $presence;
    public int $reason;

    /**
     * @param array<string, int> $attributes
     */
    public function __construct(array $attributes)
    {
        $this->control = (int)$attributes['control'];
        $this->daring = (int)$attributes['daring'];
        $this->fitness = (int)$attributes['fitness'];
        $this->insight = (int)$attributes['insight'];
        $this->presence = (int)$attributes['presence'];
        $this->reason = (int)$attributes['reason'];
    }
}
