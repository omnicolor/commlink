<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use Modules\Battletech\Enums\ExperienceItemType;

readonly class ExperienceItem
{
    public function __construct(
        public int $amount,
        public ExperienceItemType $type,
        public string $name,
    ) {
    }
}
