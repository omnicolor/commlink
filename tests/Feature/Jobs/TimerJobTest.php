<?php

declare(strict_types=1);

namespace Tests\Feature\Jobs;

use App\Jobs\TimerJob;
use App\Models\Channel;
use Carbon\CarbonInterval;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Medium;
use RuntimeException;
use Tests\TestCase;

use function str_contains;

#[Medium]
class TimerJobTest extends TestCase
{
    public function testCreateForIrc(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Can not start timer for irc channels');
        new TimerJob(
            new Channel(['type' => Channel::TYPE_IRC]),
            new CarbonInterval('1'),
            'user'
        );
    }

    public function testCreateForDiscordWithoutWebhook(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Can not start time for Discord channel without webhook',
        );
        new TimerJob(
            new Channel(['type' => Channel::TYPE_DISCORD]),
            new CarbonInterval('1'),
            'user'
        );
    }

    public function testDiscordTimer(): void
    {
        Http::preventStrayRequests();
        Http::fake(['example.com/webhook' => Response::HTTP_OK]);
        $channel = new Channel([
            'type' => Channel::TYPE_DISCORD,
            'webhook' => 'https://example.com/webhook',
        ]);

        (new TimerJob(
            $channel,
            CarbonInterval::createFromFormat('H:i', '0:1'),
            'Bob',
        ))
            ->handle();

        Http::assertSent(function (Request $request): bool {
            return 'https://example.com/webhook' === $request->url()
                && 'Bot' === $request->header('Authorization')[0]
                && str_contains(
                    $request->body(),
                    '<@Bob>, your 1 minute timer is finished',
                );
        });
    }

    public function testSlackTimer(): void
    {
        Http::preventStrayRequests();
        Http::fake(['slack.com/api/chat.postMessage' => Response::HTTP_OK]);

        $channel = new Channel([
            'channel_id' => 'C1234567',
            'type' => Channel::TYPE_SLACK,
        ]);
        (new TimerJob(
            $channel,
            CarbonInterval::createFromFormat('H:i', '0:10'),
            'Phil',
        ))->handle();

        Http::assertSent(function (Request $request): bool {
            return 'https://slack.com/api/chat.postMessage' === $request->url()
                && 'Bearer' === $request->header('Authorization')[0]
                && str_contains(
                    $request->body(),
                    '<@Phil>, your timer for 10 minutes is done.',
                );
        });
    }
}
