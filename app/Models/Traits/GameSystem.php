<?php

declare(strict_types=1);

namespace App\Models\Traits;

/**
 * Trait for changing the short system tag into the full system name.
 */
trait GameSystem
{
    public function getSystem(): string
    {
        if (!array_key_exists('system', $this->attributes)) {
            return 'Unknown';
        }

        if (array_key_exists($this->attributes['system'], config('app.systems'))) {
            return config('app.systems')[$this->attributes['system']];
        }

        return $this->attributes['system'];
    }
}
