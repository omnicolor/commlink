<?php

declare(strict_types=1);

namespace Modules\Root\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Root\ValueObjects\Attribute;

/**
 * @implements CastsAttributes<Attribute, mixed>
 */
class AttributeCast implements CastsAttributes
{
    /**
     * @param array<string, mixed> $attributes
     */
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes,
    ): Attribute {
        return new Attribute((int)$value);
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    ): int {
        if ($value instanceof Attribute) {
            return $value->value;
        }
        return $value;
    }
}
