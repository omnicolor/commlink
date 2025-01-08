<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Models;

enum AugmentationGrade: string
{
    case Alpha = 'Alpha';
    case Beta = 'Beta';
    case Delta = 'Delta';
    case Gamma = 'Gamma';
    case Omega = 'Omega';
    case Standard = 'Standard';
    case Used = 'Used';

    public function costModifier(): float
    {
        return match ($this) {
            AugmentationGrade::Alpha => 1.2,
            AugmentationGrade::Beta => 1.5,
            AugmentationGrade::Delta => 2.5,
            AugmentationGrade::Used => 0.75,
            default => 1.0,
        };
    }

    public function essenceModifier(): float
    {
        return match ($this) {
            AugmentationGrade::Alpha => 0.8,
            AugmentationGrade::Beta => 0.7,
            AugmentationGrade::Delta => 0.5,
            AugmentationGrade::Used => 1.25,
            default => 1,
        };
    }
}
