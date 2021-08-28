<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Models\Campaign;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Slack response for registering a channel for a particular campaign.
 */
class CampaignResponse extends SlackResponse
{
    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct('', $status, $headers, $channel);
        if (null === $channel) {
            throw new SlackException('Channel is required');
        }

        $args = \explode(' ', $content);
        if (2 !== \count($args)) {
            throw new SlackException(
                'To link a channel, use `campaign <campaignId>`.'
            );
        }

        if (null !== $channel->campaign) {
            throw new SlackException(\sprintf(
                'This channel is already registered for "%s".',
                $channel->campaign->name
            ));
        }

        $chatUser = $this->channel->getChatUser();
        if (null === $chatUser) {
            throw new SlackException(\sprintf(
                'You must have already created an account on <%s|%s> and '
                    . 'linked it to this server before you can register a '
                    . 'channel to a campaign.',
                config('app.url'),
                config('app.name')
            ));
        }
        $campaignId = $args[1];
        $campaign = Campaign::find($campaignId);

        if (null === $campaign) {
            throw new SlackException(
                \sprintf('No campaign was found for ID "%d".', $campaignId)
            );
        }

        if (
            $campaign->registered_by !== optional($chatUser->user)->id
            && $campaign->gamemaster !== optional($chatUser->user)->id
        ) {
            throw new SlackException(
                'You must have created the campaign or be the GM to link a '
                . 'Slack channel.'
            );
        }

        if (null !== $channel->system && $channel->system !== $campaign->system) {
            throw new SlackException(\sprintf(
                'The channel is already registered to play %s. "%s" is playing '
                    . '%s.',
                $channel->getSystem(),
                $campaign->name,
                $campaign->getSystem()
            ));
        }

        $new = false;
        if (null === $channel->registered_by) {
            // Brand new channel registration.
            $channel->server_name = $channel->getSlackTeamName($channel->server_id);
            $channel->channel_name = $channel->getSlackChannelName($channel->channel_id);
            // @phpstan-ignore-next-line
            $channel->registered_by = $chatUser->user->id;
            $new = true;
        }
        $channel->campaign_id = $campaign->id;
        $channel->system = $campaign->system;
        $channel->save();

        $this->addAttachment(new TextAttachment(
            'Registered',
            \sprintf(
                '%s has registered this channel for the "%s" campaign, playing '
                    . '%s.',
                $channel->username,
                $campaign->name,
                $campaign->getSystem()
            ),
            TextAttachment::COLOR_SUCCESS
        ))
            ->sendToChannel();
        if ($new) {
            ChannelLinked::dispatch($channel);
        }
    }
}
