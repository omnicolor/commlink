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
     * Create and return the webhook for a Discord channel.
     * @param string $snowflake
     * @return ?string
     */
    public function createDiscordWebhook(string $snowflake): ?string
    {
        $filename = public_path('images/commlink.png');
        $fileHandle = fopen($filename, 'r');
        // @codeCoverageIgnoreStart
        if (false === $fileHandle) {
            return null;
        }
        // @codeCoverageIgnoreEnd

        $logo = base64_encode(
            (string)fread($fileHandle, (int)filesize($filename))
        );
        fclose($fileHandle);

        $response = Http::withHeaders([
            'Authorization' => sprintf('Bot %s', config('app.discord_token')),
        ])
            ->post(
                sprintf(
                    'https://discord.com/api/channels/%s/webhooks',
                    $snowflake
                ),
                [
                    'name' => config('app.name'),
                    'avatar' => sprintf('data:image/png;base64,%s', $logo),
                ]
            );

        if ($response->failed()) {
            return null;
        }

        $response = $response->json();
        return sprintf(
            'https://discord.com/api/webhooks/%d/%s',
            $response['id'],
            $response['token']
        );
    }

    /**
     * Given a Discord Channel ID (snowflake), return the channel's name.
     * @param string $snowflake
     * @return ?string
     */
    public function getDiscordChannelName(string $snowflake): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bot %s', config('app.discord_token')),
        ])
            ->get(sprintf('https://discord.com/api/channels/%s', $snowflake));

        if ($response->failed()) {
            return null;
        }

        return $response['name'];
    }

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
