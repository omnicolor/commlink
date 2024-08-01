<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Rolls;

use App\Exceptions\SlackException;
use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Stillfleet\Rolls\Number;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function json_decode;

use const PHP_EOL;

#[Group('stillfleet')]
#[Medium]
final class NumberTest extends TestCase
{
    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->make(['system' => 'stillfleet']);
    }

    #[Group('slack')]
    public function testRollInvalidDieSlack(): void
    {
        self::expectException(SlackException::class);
        self::expectExceptionMessage('5 is not a valid die size in Stillfleet');
        (new Number('5', 'user', $this->channel))->forSlack();
    }

    #[Group('discord')]
    public function testRollInvalidDieDiscord(): void
    {
        $response = (new Number('99', 'user', $this->channel))->forDiscord();
        self::assertSame('99 is not a valid die size in Stillfleet', $response);
    }

    #[Group('irc')]
    public function testRollInvalidDieIrc(): void
    {
        $response = (new Number('99', 'user', $this->channel))->forIrc();
        self::assertSame('99 is not a valid die size in Stillfleet', $response);
    }

    #[Group('slack')]
    public function testRollSimpleSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(6)
            ->andReturn(3);

        $response = (new Number('6', 'user', $this->channel))->forSlack();
        $response = json_decode((string)$response);

        self::assertSame('user rolled a 3', $response->attachments[0]->title);
        self::assertSame('3', $response->attachments[0]->text);
    }

    #[Group('slack')]
    public function testRollWithBoostSlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(10)
            ->andReturn(3);

        $response = (new Number('10 2', 'user', $this->channel))->forSlack();
        $response = json_decode((string)$response);

        self::assertSame('user rolled a 5', $response->attachments[0]->title);
        self::assertSame('3 + 2', $response->attachments[0]->text);
    }

    #[Group('slack')]
    public function testRollWithPenaltySlack(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(10)
            ->andReturn(8);

        $response = (new Number('10 -2', 'user', $this->channel))->forSlack();
        $response = json_decode((string)$response);

        self::assertSame('user rolled a 6', $response->attachments[0]->title);
        self::assertSame('8 - 2', $response->attachments[0]->text);
    }

    #[Group('discord')]
    public function testRollSimpleDiscord(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(4)
            ->andReturn(4);

        $response = (new Number('4', 'user', $this->channel))->forDiscord();

        self::assertSame('**user rolled a 4**' . PHP_EOL . '4', $response);
    }

    #[Group('discord')]
    public function testRollWithBoostDiscord(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(8)
            ->andReturn(4);

        $response = (new Number('8 1', 'user', $this->channel))->forDiscord();

        self::assertSame('**user rolled a 5**' . PHP_EOL . '4 + 1', $response);
    }

    #[Group('discord')]
    public function testRollWithPenaltyDiscord(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(12)
            ->andReturn(9);

        $response = (new Number('12 -4', 'user', $this->channel))->forDiscord();

        self::assertSame('**user rolled a 5**' . PHP_EOL . '9 - 4', $response);
    }

    #[Group('irc')]
    public function testRollWithBoostIrc(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(8)
            ->andReturn(4);

        $response = (new Number('8 1', 'user', $this->channel))->forIrc();

        self::assertSame('user rolled a 5' . PHP_EOL . '4 + 1', $response);
    }

    #[Group('irc')]
    public function testRollWithPenaltyIrc(): void
    {
        DiceService::shouldReceive('rollOne')
            ->once()
            ->with(12)
            ->andReturn(9);

        $response = (new Number('12 -4', 'user', $this->channel))->forIrc();

        self::assertSame('user rolled a 5' . PHP_EOL . '9 - 4', $response);
    }
}
