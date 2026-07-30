<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Enums;

enum VoidwareType: string
{
    case Comm = 'comm';
    case Drug = 'drug';
    case Pet = 'pets';
    case Vehicle = 'vehicle';
    case Ventureware = 'ventureware';
}
