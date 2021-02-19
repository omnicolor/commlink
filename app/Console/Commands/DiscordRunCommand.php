<?php

declare(strict_types=1);

namespace App\Console\Commands;

use \CharlotteDunois\Yasmin\Client as DiscordClient;
use Illuminate\Console\Command;

/**
 * Start a Discord bot.
 */
class DiscordRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'discord:run';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Start the Discord bot server';

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $client = new DiscordClient();

        $client->on('error', function ($error): void {
            \Log::error($error);
        });

        $client->on('ready', function () use ($client): void {
            \Log::info(sprintf('Logged in to Discord: %s', $client->user->tag));
        });

        $client->on('message', function ($message): void {
            \Log::debug('Received Discord message');
        });

        $client->login(config('app.discord_token'))->done();
        $client->getLoop()->run();
        return 0;
    }
}
