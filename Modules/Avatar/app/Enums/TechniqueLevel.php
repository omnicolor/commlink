<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

enum TechniqueLevel: string
{
    case Learned = 'learned';
    case Practiced = 'practiced';
    case Mastered = 'mastered';

    public function isLearned(): bool
    {
        return true;
    }

    public function isPracticed(): bool
    {
        return match ($this) {
            TechniqueLevel::Learned => false,
            TechniqueLevel::Practiced, TechniqueLevel::Mastered => true,
        };
    }

    public function isMastered(): bool
    {
        return match ($this) {
            TechniqueLevel::Learned, TechniqueLevel::Practiced => false,
            TechniqueLevel::Mastered => true,
        };
    }
}
