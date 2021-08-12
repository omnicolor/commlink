<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Facades\Http;

/**
 * Trait for trying to get information from Discord's API.
 */
trait InteractsWithDiscord
{
    /**
     * Given a Discord User ID (snowflake), return the user's discriminator.
     * @param string $snowflake
     * @return ?string
     */
    public function getDiscordUserName(string $snowflake): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bot %s', config('app.discord_token')),
        ])
            ->get(sprintf('https://discord.com/api/users/%s', $snowflake));

        if ($response->failed()) {
            return null;
        }

        return $response['username'] . '#' . $response['discriminator'];
    }

    /**
     * Given a Discord server ID, return the server (guild) name.
     * @param string $serverId
     * @return ?string
     */
    public function getDiscordServerName(string $serverId): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bot %s', config('app.discord_token')),
        ])
            ->get(sprintf('https://discord.com/api/guilds/%s', $serverId));

        if ($response->failed()) {
            return null;
        }

        return $response['name'];
    }
}
