<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\DiscordMessageReceived;
use CharlotteDunois\Yasmin\Client;
use CharlotteDunois\Yasmin\Models\DMChannel;
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
        $client = new Client();

        $client->on('error', function ($error): void {
            \Log::error($error);
        });

        $client->on('ready', function () use ($client): void {
            if (is_null($client->user)) {
                return;
            }
            \Log::info(sprintf('Logged in to Discord: %s', $client->user->tag));
            echo 'Logged in to Discord: ', $client->user->tag, PHP_EOL;
            $this->myTag = $client->user->tag;
        });

        $client->on('message', function ($message): void {
            $content = $message->content;
            if ($this->myTag === $message->author->tag) {
                // Ignoring messages from the bot.
                \Log::debug(
                    sprintf('Ignoring message from Discord bot: %s', $content)
                );
                return;
            }
            if ($message->channel instanceof DMChannel) {
                \Log::debug(sprintf(
                    'Handling DM from %s: %s',
                    $message->author->tag,
                    $content
                ));
                $message->reply(
                    'Sorry, I\'m not supposed to talk to you outside of a '
                    . 'channel.'
                );
                return;
            }
            if (substr($content, 0, 1) !== '/') {
                // Ignore non-command chatter.
                \Log::debug(
                    sprintf('Ignoring non-command message: %s', $content)
                );
                return;
            }
            DiscordMessageReceived::dispatch($message);
        });

        $client->login(config('app.discord_token'))->done();
        $client->getLoop()->run();
        return 0;
    }
}
