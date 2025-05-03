<?php

declare(strict_types=1);

namespace Modules\Battletech\Models;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use LogicException;
use Modules\Battletech\Enums\ExperienceItemType;
use Override;

use function array_values;
use function count;

class ExperienceLog implements ArrayAccess, Countable, Iterator
{
    /** @var array<int, ExperienceItem> */
    private array $items;
    private int $pointer = 0;

    public function __construct(ExperienceItem ...$items)
    {
        $this->items = array_values($items);
    }

    #[Override]
    public function count(): int
    {
        return count($this->items);
    }

    #[Override]
    public function current(): ExperienceItem|null
    {
        return $this->items[$this->pointer];
    }

    #[Override]
    public function key(): int
    {
        return $this->pointer;
    }

    #[Override]
    public function next(): void
    {
        $this->pointer++;
    }

    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    #[Override]
    public function offsetGet(mixed $offset): ExperienceItem|null
    {
        return $this->items[$offset] ?? null;
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (null !== $offset) {
            throw new LogicException('ExperienceLog can only be appended to');
        }

        if (!$value instanceof ExperienceItem) {
            throw new InvalidArgumentException(
                'ExperienceLog can only contain ExperienceItem objects',
            );
        }
        $this->items[] = $value;
    }

    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('ExperienceLog can only be appended to');
    }

    public static function empty(): self
    {
        return new self();
    }

    #[Override]
    public function rewind(): void
    {
        $this->pointer = 0;
    }

    public function total(
        ExperienceItemType|null $type = null,
        string|null $name = null,
    ): int {
        if (null !== $type && null !== $name) {
            return collect($this->items)
                ->where('type', $type)
                ->where('name', $name)
                ->pluck('amount')
                ->sum();
        }

        if (null !== $type) {
            return collect($this->items)->where('type', $type)
                ->pluck('amount')
                ->sum();
        }

        return collect($this->items)->pluck('amount')->sum();
    }

    #[Override]
    public function valid(): bool
    {
        return isset($this->items[$this->pointer]);
    }
}
