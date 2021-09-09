<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Facades\Http;
use RuntimeException;

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

    /**
     * Gets an access token from Discord's Oauth2 service.
     * @param string $code
     * @return string
     * @throws RuntimeException
     */
    public function getDiscordAccessToken(string $code): string
    {
        $response = Http::asForm()
            ->post(
                'https://discord.com/api/oauth2/token',
                [
                    'client_id' => config('app.discord.client_id'),
                    'client_secret' => config('app.discord.client_secret'),
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => config('app.discord.redirect_uri'),
                ],
            );

        if ($response->failed()) {
            throw new RuntimeException($response->body());
        }

        $response = $response->json();
        return $response['access_token'];
    }

    /**
     * Gets a user's information from the Discord API from the Oauth2 token.
     * @param string $token
     * @return array<string, ?string>
     */
    public function getDiscordUser(string $token): array
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', $token),
        ])
            ->get('https://discord.com/api/users/@me');

        if ($response->failed()) {
            throw new RuntimeException($response->body());
        }

        $user = $response->json();
        $avatar = null;
        if (null !== $user['avatar']) {
            $avatar = sprintf(
                'https://cdn.discordapp.com/avatars/%s/%s.png',
                $user['id'],
                $user['avatar'],
            );
        }
        return [
            'avatar' => $avatar,
            'discriminator' => $user['discriminator'],
            'snowflake' => $user['id'],
            'username' => $user['username'],
        ];
    }

    /**
     * Get a user's guilds from the Discord API.
     * @param string $token
     * @return array<int, array<string, ?string>>
     * @throws RuntimeException
     */
    public function getDiscordGuilds(string $token): array
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', $token),
        ])
            ->get('https://discord.com/api/users/@me/guilds');

        if ($response->failed()) {
            throw new RuntimeException($response->body());
        }

        $rawGuilds = $response->json();
        $guilds = [];
        foreach ($rawGuilds as $guild) {
            $icon = null;
            if (null !== $guild['icon']) {
                $icon = sprintf(
                    'https://cdn.discordapp.com/icons/%s/%s.png',
                    $guild['id'],
                    $guild['icon'],
                );
            }
            $guilds[] = [
                'icon' => $icon,
                'name' => $guild['name'],
                'snowflake' => $guild['id'],
            ];
        }
        return $guilds;
    }

    /**
     * Return the Oauth2 URL for linking Discord.
     * @return string
     */
    public function getDiscordOauthURL(): string
    {
        return 'https://discord.com/api/oauth2/authorize?' . \http_build_query([
            'client_id' => config('app.discord.client_id'),
            'redirect_uri' => config('app.discord.redirect_uri'),
            'response_type' => 'code',
            'scope' => 'identify guilds',
        ]);
    }
}
