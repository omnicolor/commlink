<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Modules\Shadowrun6e\Enums\LanguageLevel;
use Override;
use Stringable;

use function sprintf;

readonly class Language extends KnowledgeSkill implements Stringable
{
    /**
     * @phpstan-ignore constructor.missingParentCall
     */
    public function __construct(
        public string $name,
        public LanguageLevel $level = LanguageLevel::Fluent,
    ) {
    }

    #[Override]
    public function __toString(): string
    {
        return sprintf(
            '%s%s',
            $this->name,
            match ($this->level) {
                LanguageLevel::Specialist => ' (S)',
                LanguageLevel::Expert => ' (E)',
                LanguageLevel::Native => ' (N)',
                default => '',
            },
        );
    }
}
