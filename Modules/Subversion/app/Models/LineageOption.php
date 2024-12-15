<?php

declare(strict_types=1);

namespace Modules\Subversion\Models;

readonly class LineageOption
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
