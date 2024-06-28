<?php

declare(strict_types=1);

namespace Modules\Avatar\Models;

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
