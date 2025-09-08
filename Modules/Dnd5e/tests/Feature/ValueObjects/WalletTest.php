<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\ValueObjects;

use LogicException;
use Modules\Dnd5e\Enums\CoinType;
use Modules\Dnd5e\ValueObjects\Wallet;
use OutOfBoundsException;
use Override;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

use function json_encode;

use const JSON_THROW_ON_ERROR;

#[Group('dnd5e')]
#[Small]
final class WalletTest extends TestCase
{
    private Wallet $wallet;

    #[Override]
    protected function setUp(): void
    {
        $this->wallet = Wallet::make();
    }

    public function testManuallySetting(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('Wallet can not be directly modified');
        $this->wallet[CoinType::Gold] = 5;
    }

    public function testManuallyUnsetting(): void
    {
        self::expectException(LogicException::class);
        self::expectExceptionMessage('Wallet can not be directly modified');
        unset($this->wallet[CoinType::Gold]);
    }

    public function testGetInvalidCoinType(): void
    {
        self::expectException(OutOfBoundsException::class);
        self::expectExceptionMessage('Invalid coin type');
        // @phpstan-ignore expr.resultUnused
        $this->wallet['foo'];
    }

    public function testEmptyWallet(): void
    {
        self::assertSame(0, $this->wallet[CoinType::Platinum]);
        self::assertSame(0, $this->wallet[CoinType::Gold]);
        self::assertSame(0, $this->wallet[CoinType::Silver]);
        self::assertSame(0, $this->wallet[CoinType::Copper]);
        self::assertSame(0, $this->wallet[CoinType::Electrum]);
        self::assertCount(0, $this->wallet);
    }

    public function testIsset(): void
    {
        self::assertArrayHasKey(CoinType::Platinum, $this->wallet);
        self::assertArrayHasKey(CoinType::Gold, $this->wallet);
        self::assertArrayHasKey(CoinType::Silver, $this->wallet);
        self::assertArrayHasKey(CoinType::Copper, $this->wallet);
        self::assertArrayHasKey(CoinType::Electrum, $this->wallet);
        self::assertArrayNotHasKey(0, $this->wallet);
    }

    public function testAdd(): void
    {
        self::assertSame(0, $this->wallet[CoinType::Platinum]);
        $this->wallet->add(CoinType::Platinum, 100);
        $this->wallet->add(CoinType::Electrum, 200);
        self::assertSame(100, $this->wallet[CoinType::Platinum]);
        self::assertSame(200, $this->wallet[CoinType::Electrum]);
        self::assertCount(300, $this->wallet);
    }

    public function testSubtract(): void
    {
        $this->wallet->add(CoinType::Gold, 100);
        $this->wallet->subtract(CoinType::Gold, 50);
        self::assertSame(50, $this->wallet[CoinType::Gold]);
    }

    public function testIterate(): void
    {
        $wallet = Wallet::make(
            platinum: 20,
            gold: 30,
            silver: 40,
            copper: 50,
            electrum: 60,
        );

        $expected = [
            (object)['key' => CoinType::Platinum, 'value' => 20],
            (object)['key' => CoinType::Gold, 'value' => 30],
            (object)['key' => CoinType::Silver, 'value' => 40],
            (object)['key' => CoinType::Copper, 'value' => 50],
            (object)['key' => CoinType::Electrum, 'value' => 60],
        ];
        $index = 0;
        foreach ($wallet as $key => $value) {
            self::assertSame($expected[$index]->key, $key);
            self::assertSame($expected[$index]->value, $value);
            ++$index;
        }
        self::assertNull($wallet->current());
    }

    public function testJsonSerializeEmpty(): void
    {
        self::assertSame(
            '{"pp":0,"gp":0,"sp":0,"cp":0,"ep":0}',
            json_encode($this->wallet, JSON_THROW_ON_ERROR),
        );
    }

    public function testJsonSerialize(): void
    {
        $wallet = Wallet::make(20, 30, 40, 50, 60);
        self::assertSame(
            '{"pp":20,"gp":30,"sp":40,"cp":50,"ep":60}',
            json_encode($wallet, JSON_THROW_ON_ERROR),
        );
    }

    public function testFromJsonInvalid(): void
    {
        $wallet = Wallet::fromJson('{');
        self::assertSame(0, $wallet[CoinType::Platinum]);
        self::assertSame(0, $wallet[CoinType::Gold]);
        self::assertSame(0, $wallet[CoinType::Silver]);
        self::assertSame(0, $wallet[CoinType::Copper]);
        self::assertSame(0, $wallet[CoinType::Electrum]);
    }

    public function testFromJsonWithInvalidValues(): void
    {
        $wallet = Wallet::fromJson('{"foo":"bar","cp":30}');
        self::assertSame(0, $wallet[CoinType::Platinum]);
        self::assertSame(30, $wallet[CoinType::Copper]);
    }

    public function testFromJson(): void
    {
        $wallet = Wallet::fromJson('{"pp":1,"gp":2,"sp":3,"cp":4,"ep":5}');
        self::assertCount(15, $wallet);
    }
}
