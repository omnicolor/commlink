<?php

declare(strict_types=1);

namespace App\Models\Avatar\Moves;

abstract class Move
{
    public string $attribute;
    public string $name;
    public string $id;
    public string $type;
}
