<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;

/**
 * Lifestyle attributes.
 */
final class LifestyleAttributes
{
    public readonly int $comforts;
    public readonly int $comfortsMax;
    public int $neighborhood;
    public readonly int $neighborhoodMax;
    public readonly int $security;
    public readonly int $securityMax;

    /**
     * @param array<string, int> $attributes
     * @throws RuntimeException if attributes are missing
     */
    public function __construct(array $attributes)
    {
        if (
            !isset(
                $attributes['comforts'],
                $attributes['comfortsMax'],
                $attributes['neighborhood'],
                $attributes['neighborhoodMax'],
                $attributes['security'],
                $attributes['securityMax'],
            )
        ) {
            throw new RuntimeException('Lifestyle attributes missing');
        }

        $this->comforts = (int)$attributes['comforts'];
        $this->comfortsMax = (int)$attributes['comfortsMax'];
        $this->neighborhood = (int)$attributes['neighborhood'];
        $this->neighborhoodMax = (int)$attributes['neighborhoodMax'];
        $this->security = (int)$attributes['security'];
        $this->securityMax = (int)$attributes['securityMax'];
    }
}
