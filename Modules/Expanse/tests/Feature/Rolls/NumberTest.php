<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Modules\Expanse\Rolls\Number;
use Omnicolor\Slack\Attachment;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Medium;
use Tests\TestCase;

use function explode;

use const PHP_EOL;

#[Group('expanse')]
#[Medium]
final class NumberTest extends TestCase
{
    /**
     * Test a basic roll generating stunt points in Slack without a description.
     */
    #[Group('slack')]
    public function testSimpleRollSlack(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([3, 3, 3]);

        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Number('5', 'user', $channel))
            ->forSlack()
            ->jsonSerialize();

        self::assertArrayHasKey('attachments', $response);
        self::assertSame(
            [
                'color' => Attachment::COLOR_SUCCESS,
                'footer' => '3 3 `3`',
                'text' => '14 (3 SP)',
                'title' => 'user made a roll',
            ],
            $response['attachments'][0],
        );
    }

    /**
     * Test a basic roll generating stunt points in Discord without a
     * description.
     */
    #[Group('discord')]
    public function testSimpleRollDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([3, 3, 3]);

        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Number('5', 'user', $channel))->forDiscord();
        $response = explode(PHP_EOL, $response);
        self::assertSame('**user made a roll**', $response[0]);
        self::assertSame('14 (3 SP)', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in Discord with a
     * description.
     */
    #[Group('discord')]
    public function testRollWithDescriptionDiscord(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 3, 5]);

        $channel = Channel::factory()->make(['system' => 'expanse']);
        $response = (new Number('5 percept', 'user', $channel))->forDiscord();
        $response = explode(PHP_EOL, $response);
        self::assertSame('**user made a roll for "percept"**', $response[0]);
        self::assertSame('15', $response[1]);
    }

    /**
     * Test a basic roll not generating stunt points in IRC with a description.
     */
    #[Group('irc')]
    public function testRollWithDescriptionIrc(): void
    {
        DiceService::shouldReceive('rollMany')
            ->once()
            ->with(3, 6)
            ->andReturn([2, 3, 5]);

        $channel = Channel::factory()->make(['system' => 'expanse']);

        $response = (new Number('5 percept', 'user', $channel))->forIrc();
        $response = explode(PHP_EOL, $response);
        self::assertSame('user made a roll for "percept"', $response[0]);
        self::assertSame('15', $response[1]);
    }
}
