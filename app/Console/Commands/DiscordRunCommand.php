<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\DiscordMessageReceived;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\WebSockets\Event;
use Illuminate\Console\Command;

/**
 * Start a Discord bot.
 * @codeCoverageIgnore
 */
class DiscordRunCommand extends Command
{
    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Start the Discord bot server';

    /**
     * The tag for the bot.
     * @var string
     */
    protected string $myTag;

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'discord:run';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $discord = new Discord(['token' => config('app.discord_token')]);
        $discord->on('ready', function (Discord $discord): void {
            echo 'Logged in to Discord', \PHP_EOL;
        });
        $discord->on(Event::MESSAGE_CREATE, function (Message $message, Discord $discord): void {
            // @phpstan-ignore-next-line
            if ($message->author->bot) {
                // Ignore messages from bots.
                return;
            }
            if ('/' !== \substr($message->content, 0, 1)) {
                // Ignore non-command chatter.
                return;
            }
            DiscordMessageReceived::dispatch($message);
        });

        $discord->run();
        return self::SUCCESS;
    }
}
