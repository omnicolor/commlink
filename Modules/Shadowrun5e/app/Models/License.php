<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

use Override;
use Stringable;

use function sprintf;

/**
 * Representation of a Shadowrun 5E fake license.
 */
final class License implements Stringable
{
    public function __construct(
        public readonly int $rating,
        public readonly string $name,
    ) {
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf('%s (%d)', $this->name, $this->rating);
    }

    public function getCost(): int
    {
        return $this->rating * 200;
    }
}
