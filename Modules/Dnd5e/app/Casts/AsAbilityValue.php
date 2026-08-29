<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Dnd5e\ValueObjects\AbilityValue;

class AsAbilityValue implements CastsAttributes
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
    ): AbilityValue {
        return new AbilityValue($value);
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
    ): int {
        if ($value instanceof AbilityValue) {
            return $value->value;
        }
        return (int)$value;
    }
}
