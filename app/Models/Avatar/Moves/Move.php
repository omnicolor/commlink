<?php

declare(strict_types=1);

namespace App\Models\Avatar\Moves;

/**
 * @psalm-suppress UnusedClass
 */
abstract class Move
{
    public string $attribute;
    public string $name;
    public string $id;
    public string $type;
}
