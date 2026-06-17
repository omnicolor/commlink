<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Modules\Shadowrun6e\Enums\SpecializationLevel;
use Override;
use Stringable;

use function sprintf;

readonly class SkillSpecialization implements Stringable
{
    public SpecializationLevel $level;

    public function __construct(
        public string $name,
        SpecializationLevel|null $level = null,
    ) {
        $this->level = $level ?? SpecializationLevel::Specialization;
    }

    #[Override]
    public function __toString(): string
    {
        if (SpecializationLevel::Expertise === $this->level) {
            return sprintf('%s (E)', $this->name);
        }
        return $this->name;
    }
}
