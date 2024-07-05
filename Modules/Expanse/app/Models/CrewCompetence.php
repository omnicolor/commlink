<?php

declare(strict_types=1);

namespace Modules\Expanse\Models;

enum CrewCompetence: string
{
    case Incompetent = 'Incompetent';
    case Poor = 'Poor';
    case Average = 'Average';
    case Capable = 'Capable';
    case Skilled = 'Skilled';
    case Elite = 'Elite';

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
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
