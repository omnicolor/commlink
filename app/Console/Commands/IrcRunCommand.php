<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\IrcMessageReceived;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;

/**
 * Start an IRC bot.
 * @codeCoverageIgnore
 * @psalm-suppress UnusedClass
 */
class IrcRunCommand extends Command
{
    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Start the IRC bot server';

    /**
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:irc-run
                            {server : Hostname of the server to connect to}
                            {--port=6667 : Port to connect to}
                            {--nickname : Nickname to use in IRC, (defaults to the App\'s name)}
                            {--channel=* : Channel(s) to automatically connect to (defaults to #commlink)}';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $server = $this->argument('server');
        $port = $this->option('port');
        $nickname = $this->option('nickname') ?: config('app.name');
        $channels = $this->option('channel') ?: ['#commlink'];

        $client = new IrcClient(
            \sprintf('%s:%s', $server, $port),
            new ClientOptions(nickname: $nickname, channels: $channels),
        );

        $client->on('registered', function () use ($server, $port): void {
            $message = \sprintf('Connected to %s, port %s', $server, $port);
            $this->line($message);
            Log::info($message);
        });

        $client->on(
            'message',
            function (string $from, IrcChannel $channel, string $message) use ($client): void {
                if (':roll' !== substr($message, 0, 5)) {
                    // Ignore non-colon messages.
                    Log::debug(
                        'IRC - Ignoring message in ' . $channel->getName()
                            . ' - ' . $from . ' - ' . $message
                    );
                    return;
                }

                $logMessage = \sprintf(
                    ' . %10s - %12s - %s',
                    $channel->getName(),
                    $from,
                    $message,
                );
                $this->line($logMessage);
                Log::debug('IRC ' . $logMessage);

                IrcMessageReceived::dispatch($message, $from, $client, $channel);
            }
        );

        $client->connect();
        return self::SUCCESS;
    }
}
