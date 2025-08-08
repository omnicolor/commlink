<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

enum MatrixMode: string
{
    case None = 'none';
    case AugmentedReality = 'ar';
    case VirtualReality = 'vr';
}
