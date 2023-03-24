<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\DiscordMessageReceived;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * Start a Discord bot.
 * @codeCoverageIgnore
 */
class DiscordRunCommand extends Command
{
    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Start the Discord bot server';

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:discord-run';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $discord = new Discord([
            'logger' => Log::getLogger(),
            'storeMessages' => true,
            'token' => config('services.discord.token'),
        ]);
        $discord->on('ready', function (): void {
            echo 'Logged in to Discord', \PHP_EOL;
        });
        $discord->on(
            Event::MESSAGE_CREATE,
            function (Message $message, Discord $discord): void {
                // @phpstan-ignore-next-line
                if ($message->author->bot) {
                    // Ignore messages from bots.
                    return;
                }
                if ('/' !== \substr($message->content, 0, 1)) {
                    // Ignore non-command chatter.
                    return;
                }
                DiscordMessageReceived::dispatch($message, $discord);
            }
        );

        $discord->run();
        return self::SUCCESS;
    }
}
