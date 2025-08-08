<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Enums;

enum MatrixAttribute: string
{
    case Attack = 'attack';
    case DataProcessing = 'data_processing';
    case Firewall = 'firewall';
    case Sleaze = 'sleaze';
}
