<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

use Override;
use Stringable;

readonly class LineageOption implements Stringable
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ) {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
