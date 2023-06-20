<?php

declare(strict_types=1);

namespace Tests\Feature\Rolls\Transformers;

use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Transformers\Number;
use Illuminate\Foundation\Testing\RefreshDatabase;
use phpmock\phpunit\PHPMock;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

/**
 * @group discord
 * @group transformers
 * @group slack
 * @medium
 */
final class NumberTest extends TestCase
{
    use PHPMock;
    use RefreshDatabase;

    protected MockObject $randomInt;

    public function setUp(): void
    {
        parent::setUp();
        $this->randomInt = $this->getFunctionMock(
            'App\\Rolls\\Transformers',
            'random_int'
        );
    }

    public function testRollSlackFailure(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = (new Number('5', 'user', $channel))->forSlack();
        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_DANGER,
                    'text' => '5 >= 5',
                    'title' => 'user rolled a failure',
                ],
            ],
        ];
        self::assertSame($expected, $response->original);
    }

    public function testRollSlackSuccessWithDescription(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        $response = (new Number('10 espionage', 'user', $channel))->forSlack();
        $expected = [
            'response_type' => 'in_channel',
            'attachments' => [
                [
                    'color' => TextAttachment::COLOR_SUCCESS,
                    'text' => '5 < 10',
                    'title' => 'user rolled a success for "espionage"',
                ],
            ],
        ];
        self::assertSame($expected, $response->original);
    }

    public function testRollDiscord(): void
    {
        /** @var Channel */
        $channel = Channel::factory()->make(['system' => 'cyberpunkred']);
        $channel->username = 'user';
        $this->randomInt->expects(self::exactly(1))->willReturn(5);
        self::assertSame(
            '**user rolled a success**' . \PHP_EOL . '5 < 10',
            (new Number('10', 'user', $channel))->forDiscord()
        );
    }
}
