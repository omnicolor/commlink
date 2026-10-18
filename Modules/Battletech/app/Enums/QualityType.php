<?php

declare(strict_types=1);

namespace Modules\Battletech\Enums;

enum QualityType: string
{
    case Flexible = 'flexible';
    case Multiple = 'multiple';
    case Negative = 'negative';
    case Opposed = 'opposed';
    case Positive = 'positive';
}
