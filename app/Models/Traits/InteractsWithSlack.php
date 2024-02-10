<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Illuminate\Support\Facades\Http;

/**
 * Trait for trying to get information from Slack's API.
 */
trait InteractsWithSlack
{
    /**
     * Return the name of a Slack channel ID.
     */
    public function getSlackChannelName(string $channelId): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', config('app.slack_token')),
        ])->get(
            'https://slack.com/api/conversations.info',
            ['channel' => \urlencode($channelId)]
        );

        // Most Slack APIs don't fail, they return success with a false OK
        // field.
        if ($response->failed() || false === $response['ok']) {
            return null;
        }

        return $response['channel']['name'];
    }

    /**
     * Using a Slack team ID, try to return the name of the team.
     */
    public function getSlackTeamName(string $slackTeam): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', config('app.slack_token')),
        ])->get('https://slack.com/api/auth.teams.list');

        // Most Slack APIs don't fail, they return success with a false OK
        // field.
        if ($response->failed() || false === $response['ok']) {
            return null;
        }

        // There could be more than one Slack workspace the bot has been
        // invited to, so we'll potentially need to go through all of them to
        // find the right one.
        foreach ($response['teams'] as $team) {
            if ($team['id'] === $slackTeam) {
                return $team['name'];
            }
        }
        return null;
    }

    /**
     * Given a Slack User ID, return the user's name.
     */
    public function getSlackUserName(string $user): ?string
    {
        $response = Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', config('app.slack_token')),
        ])
            ->get(
                'https://slack.com/api/users.info',
                ['user' => \urlencode($user)]
            );

        if ($response->failed() || false === $response['ok']) {
            return null;
        }

        return $response['user']['name'];
    }
}
