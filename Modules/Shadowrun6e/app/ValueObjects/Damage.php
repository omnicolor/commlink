<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Modules\Shadowrun6e\Enums\DamageType;
use Override;
use Stringable;

use function sprintf;

readonly class Damage implements Stringable
{
    public function __construct(
        public DamageType $type,
        public int $amount,
    ) {
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf('%d%s', $this->amount, $this->type->value);
    }
}
