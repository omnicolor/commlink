<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Campaign as CampaignModel;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Models\User;

class Campaign extends Roll
{
    protected const MIN_NUM_ARGUMENTS = 2;

    /**
     * Campaign the user wants to link the channel to.
     * @var ?CampaignModel
     */
    protected ?CampaignModel $campaign = null;

    /**
     * Campaign ID to link the channel to.
     * @var ?int
     */
    protected ?int $campaignId = null;

    /**
     * Campaign already linked to the channel.
     * @var ?CampaignModel
     */
    protected ?CampaignModel $existingCampaign = null;

    /**
     * Constructor.
     * @param string $content
     * @param string $character
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        parent::__construct($content, $character, $channel);
        $args = \explode(' ', $content);
        if (self::MIN_NUM_ARGUMENTS === \count($args)) {
            $this->campaignId = (int)$args[1];
            $this->campaign = CampaignModel::find($this->campaignId);
        }
        $this->chatUser = $this->channel->getChatUser();
        $this->existingCampaign = $channel->campaign;
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        if (null === $this->campaignId) {
            throw new SlackException(
                'To link a campaign to this channel, use '
                    . '`campaign <campaignId>`.'
            );
        }

        if (null !== $this->existingCampaign) {
            throw new SlackException(\sprintf(
                'This channel is already registered for "%s".',
                $this->existingCampaign->name
            ));
        }

        if (null === $this->chatUser) {
            throw new SlackException(\sprintf(
                'You must have already created an account on <%s|%s> and '
                    . 'linked it to this server before you can register a '
                    . 'channel to a campaign.',
                config('app.url'),
                config('app.name')
            ));
        }

        if (null === $this->campaign) {
            throw new SlackException(\sprintf(
                'No campaign was found for ID "%d".',
                $this->campaignId
            ));
        }

        if (
            null !== $this->channel->system
            && $this->channel->system !== $this->campaign->system
        ) {
            throw new SlackException(\sprintf(
                'The channel is already registered to play %s. "%s" is playing '
                    . '%s.',
                $this->channel->getSystem(),
                $this->campaign->name,
                $this->campaign->getSystem()
            ));
        }

        if (!$this->userCanLink()) {
            throw new SlackException(
                'You must have created the campaign or be the GM to link a '
                    . 'Slack channel.'
            );
        }

        $this->linkCampaignToChannel();

        $attachment = new TextAttachment(
            'Registered',
            \sprintf(
                '%s has registered this channel for the "%s" campaign, playing '
                    . '%s.',
                $this->channel->username,
                // @phpstan-ignore-next-line
                $this->campaign->name,
                // @phpstan-ignore-next-line
                $this->campaign->getSystem()
            ),
            TextAttachment::COLOR_SUCCESS
        );
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        if (null === $this->campaignId) {
            return 'To link a campaign to this channel, use '
                . '`campaign <campaignId>`.';
        }

        if (null !== $this->existingCampaign) {
            return \sprintf(
                'This channel is already registered for "%s".',
                $this->existingCampaign->name
            );
        }

        if (null === $this->chatUser) {
            return \sprintf(
                'You must have already created an account on %s (%s) and '
                    . 'linked it to this server before you can register a '
                    . 'channel to a campaign.',
                config('app.name'),
                config('app.url'),
            );
        }

        if (null === $this->campaign) {
            return \sprintf(
                'No campaign was found for ID "%d".',
                $this->campaignId
            );
        }

        if (
            null !== $this->channel->system
            && $this->channel->system !== $this->campaign->system
        ) {
            return \sprintf(
                'The channel is already registered to play %s. "%s" is playing '
                    . '%s.',
                $this->channel->getSystem(),
                $this->campaign->name,
                $this->campaign->getSystem()
            );
        }

        if (!$this->userCanLink()) {
            return 'You must have created the campaign or be the GM to link a '
                . 'Slack channel.';
        }

        $this->linkCampaignToChannel();

        return \sprintf(
            '%s has registered this channel for the "%s" campaign, playing %s.',
            $this->channel->username,
            // @phpstan-ignore-next-line
            $this->campaign->name,
            // @phpstan-ignore-next-line
            $this->campaign->getSystem()
        );
    }

    protected function linkCampaignToChannel(): void
    {
        $new = false;
        if (null === $this->channel->registered_by) {
            // Brand new channel registration.
            $this->channel->server_name = $this->channel->getSlackTeamName(
                $this->channel->server_id
            );
            $this->channel->channel_name = $this->channel->getSlackChannelName(
                $this->channel->channel_id
            );
            // @phpstan-ignore-next-line
            $this->channel->registered_by = $this->chatUser->user->id;
            $new = true;
        }
        // @phpstan-ignore-next-line
        $this->channel->campaign_id = $this->campaign->id;
        // @phpstan-ignore-next-line
        $this->channel->system = $this->campaign->system;
        $this->channel->save();

        if ($new) {
            ChannelLinked::dispatch($this->channel);
        }
    }

    /**
     * Determine whether the user is allowed to link the campaign to the
     * channel.
     * @return bool
     */
    protected function userCanLink(): bool
    {
        return optional($this->campaign)->registered_by === optional($this->chatUser)->user->id
            || optional($this->campaign)->gm === optional($this->chatUser)->user->id;
    }
}
