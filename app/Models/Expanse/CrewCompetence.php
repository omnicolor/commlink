<?php

declare(strict_types=1);

namespace App\Models\Expanse;

enum CrewCompetence: string
{
    case Incompetent = 'Incompetent';
    case Poor = 'Poor';
    case Average = 'Average';
    case Capable = 'Capable';
    case Skilled = 'Skilled';
    case Elite = 'Elite';

    public function bonus(): int
    {
        return match ($this) {
            CrewCompetence::Incompetent => 0,
            CrewCompetence::Poor => 1,
            CrewCompetence::Average => 2,
            CrewCompetence::Capable => 3,
            CrewCompetence::Skilled => 4,
            CrewCompetence::Elite => 5,
        };
    }
}
