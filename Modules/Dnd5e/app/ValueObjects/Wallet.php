<?php

declare(strict_types=1);

namespace Modules\Dnd5e\ValueObjects;

use ArrayAccess;
use Countable;
use Iterator;
use JsonException;
use JsonSerializable;
use LogicException;
use Modules\Dnd5e\Enums\CoinType;
use OutOfBoundsException;
use Override;

use function json_decode;

use const JSON_THROW_ON_ERROR;

class Wallet implements ArrayAccess, Countable, Iterator, JsonSerializable
{
    /**
     * @var array{
     *     pp: int,
     *     gp: int,
     *     sp: int,
     *     cp: int,
     *     ep: int
     * }
     */
    private array $coins;

    private CoinType|null $position;

    private function __construct()
    {
        $this->coins = [
            CoinType::Platinum->value => 0,
            CoinType::Gold->value => 0,
            CoinType::Silver->value => 0,
            CoinType::Copper->value => 0,
            CoinType::Electrum->value => 0,
        ];
    }

    public function add(CoinType $coinType, int $amount): self
    {
        $this->coins[$coinType->value] += $amount;
        return $this;
    }

    #[Override]
    public function count(): int
    {
        // @phpstan-ignore return.type
        return $this->coins[CoinType::Platinum->value]
            + $this->coins[CoinType::Gold->value]
            + $this->coins[CoinType::Silver->value]
            + $this->coins[CoinType::Copper->value]
            + $this->coins[CoinType::Electrum->value];
    }

    #[Override]
    public function current(): int|null
    {
        if (null === $this->position) {
            return null;
        }
        return $this->coins[$this->position->value];
    }

    public static function fromJson(string $value): self
    {
        try {
            $coins = json_decode(
                json: $value,
                associative: true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (JsonException) {
            return self::make();
        }

        return self::make(
            $coins['pp'] ?? 0,
            $coins['gp'] ?? 0,
            $coins['sp'] ?? 0,
            $coins['cp'] ?? 0,
            $coins['ep'] ?? 0,
        );
    }

    /**
     * @return array{pp: int, gp: int, sp: int, cp: int, ep: int}
     */
    #[Override]
    public function jsonSerialize(): mixed
    {
        return $this->coins;
    }

    #[Override]
    public function key(): CoinType|null
    {
        return $this->position;
    }

    public static function make(
        int $platinum = 0,
        int $gold = 0,
        int $silver = 0,
        int $copper = 0,
        int $electrum = 0,
    ): self {
        $wallet = new self();
        $wallet->coins[CoinType::Platinum->value] = $platinum;
        $wallet->coins[CoinType::Gold->value] = $gold;
        $wallet->coins[CoinType::Silver->value] = $silver;
        $wallet->coins[CoinType::Copper->value] = $copper;
        $wallet->coins[CoinType::Electrum->value] = $electrum;
        return $wallet;
    }

    #[Override]
    public function next(): void
    {
        $this->position = match ($this->position) {
            CoinType::Copper => CoinType::Electrum,
            CoinType::Gold => CoinType::Silver,
            CoinType::Silver => CoinType::Copper,
            CoinType::Platinum => CoinType::Gold,
            default => null,
        };
    }

    #[Override]
    public function offsetExists(mixed $offset): bool
    {
        return match ($offset) {
            CoinType::Copper => true,
            CoinType::Electrum => true,
            CoinType::Gold => true,
            CoinType::Silver => true,
            CoinType::Platinum => true,
            default => false,
        };
    }

    #[Override]
    public function offsetGet(mixed $offset): mixed
    {
        return match ($offset) {
            CoinType::Copper => $this->coins[CoinType::Copper->value],
            CoinType::Electrum => $this->coins[CoinType::Electrum->value],
            CoinType::Gold => $this->coins[CoinType::Gold->value],
            CoinType::Silver => $this->coins[CoinType::Silver->value],
            CoinType::Platinum => $this->coins[CoinType::Platinum->value],
            default => throw new OutOfBoundsException('Invalid coin type'),
        };
    }

    #[Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException('Wallet can not be directly modified');
    }

    #[Override]
    public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('Wallet can not be directly modified');
    }

    #[Override]
    public function rewind(): void
    {
        $this->position = CoinType::Platinum;
    }

    public function subtract(CoinType $coinType, int $amount): self
    {
        $this->coins[$coinType->value] -= $amount;
        return $this;
    }

    #[Override]
    public function valid(): bool
    {
        return $this->offsetExists($this->position);
    }
}
