<?php

declare(strict_types=1);

namespace App\Casts;

use App\ValueObjects\Email;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class AsEmail implements CastsAttributes
{
    /**
     * Cast the given value.
     * @param array<string, mixed> $attributes
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): Email {
        return new Email($value);
    }

    /**
     * Prepare the given value for storage.
     * @param array<string, mixed> $attributes
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): string {
        return (string)$value;
    }
}
