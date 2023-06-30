<?php

declare(strict_types=1);

namespace App\Models\Avatar;

/**
 * @psalm-suppress UnusedClass
 */
abstract class Status
{
    public const TYPE_NEGATIVE = 'negative';
    public const TYPE_POSITIVE = 'positive';

    public string $description;
    public string $id;
    public string $name;
    public string $type;
}
