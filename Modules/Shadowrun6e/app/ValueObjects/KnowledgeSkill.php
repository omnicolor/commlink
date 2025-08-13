<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Override;
use Stringable;

readonly class KnowledgeSkill implements Stringable
{
    public function __construct(public string $name)
    {
    }

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }
}
