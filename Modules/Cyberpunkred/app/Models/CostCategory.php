<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Models;

enum CostCategory: string
{
    case Cheap = 'Cheap';
    case Costly = 'Costly';
    case Everyday = 'Everyday';
    case Expensive = 'Expensive';
    case Luxury = 'Luxury';
    case Premium = 'Premium';
    case SuperLuxury = 'Super Luxury';
    case VeryExpensive = 'V. Expensive';

    public function marketPrice(): int
    {
        return match ($this) {
            CostCategory::Cheap => 10,
            CostCategory::Everyday => 20,
            CostCategory::Costly => 50,
            CostCategory::Premium => 100,
            CostCategory::Expensive => 500,
            CostCategory::VeryExpensive => 1000,
            CostCategory::Luxury => 5000,
            CostCategory::SuperLuxury => 10000,
        };
    }
}
