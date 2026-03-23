<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\ValueObjects;

use Modules\Shadowrun6e\Models\Character;
use OutOfRangeException;
use Override;
use RuntimeException;
use Stringable;

/**
 * @property int $base_value
 * @property int $value
 */
readonly class Attribute implements Stringable
{
    public function __construct(
        private int $base_value,
        private Character $character,
        private string $name,
    ) {
        if ($base_value < 0) {
            throw new OutOfRangeException('Attribute value must be greater or equal to 0');
        }
        if ($base_value > 10) {
            throw new OutOfRangeException('Attribute value must be less or equal 10');
        }
    }

    public function __get(string $name): int|null
    {
        if ('base_value' === $name) {
            return $this->base_value;
        }
        if ('value' === $name) {
            return $this->value();
        }
        return null;
    }

    public function __isset(string $name): bool
    {
        return 'base_value' === $name || 'value' === $name;
    }

    public function __set(string $name, mixed $value): void
    {
        throw new RuntimeException('Attributes are immutable');
    }

    #[Override]
    public function __toString(): string
    {
        return (string)$this->value();
    }

    private function value(): int
    {
        // TODO: Add modifiers for spells, or augmentations.
        $value = $this->base_value;
        foreach ($this->character->qualities as $quality) {
            foreach ($quality->effects as $effect => $amount) {
                if ($effect !== $this->name) {
                    continue;
                }
                $value += $amount;
            }
        }
        return $value;
    }
}
