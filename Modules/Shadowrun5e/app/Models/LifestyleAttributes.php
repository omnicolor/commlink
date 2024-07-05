<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use RuntimeException;

/**
 * Lifestyle attributes.
 * @psalm-suppress PossiblyUnusedProperty
 */
class LifestyleAttributes
{
    public int $comforts;
    public int $comfortsMax;
    public int $neighborhood;
    public int $neighborhoodMax;
    public int $security;
    public int $securityMax;

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
