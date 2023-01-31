<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;

/**
 * Start an IRC bot.
 * @codeCoverageIgnore
 */
class IrcRunCommand extends Command
{
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Start the IRC bot server';

    /**
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:irc-run';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $server = config('app.irc.server');
        $port = config('app.irc.port');

        $client = new IrcClient(
            \sprintf('%s:%s', $server, $port),
            new ClientOptions(
                nickname: config('app.irc.bot_name'),
                channels: ['#commlink'],
            ),
        );

        $client->on('registered', function () use ($server, $port): void {
            $this->line(\sprintf('Connected to %s, port %s', $server, $port));
        });

        $client->on(
            'message',
            function (string $from, IrcChannel $channel, string $message) use ($client): void {
                if (':roll' !== substr($message, 0, 5)) {
                    // Ignore non-colon messages.
                    return;
                }

                $this->line(\sprintf(
                    ' . %10s - %12s - %s',
                    $channel->getName(),
                    $from,
                    $message,
                ));

                IrcMessageReceived::dispatch($message, $from, $client, $channel);
            }
        );

        $client->connect();
        return self::SUCCESS;
    }
}
