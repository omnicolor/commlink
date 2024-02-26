<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Events\ChannelLinked;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Campaign as CampaignModel;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

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

    protected ?string $error = null;
    protected string $message;

    public function __construct(
        string $content,
        string $character,
        protected Channel $channel
    ) {
        parent::__construct($content, $character, $channel);
        $args = \explode(' ', $content);
        if (self::MIN_NUM_ARGUMENTS === \count($args)) {
            $this->campaignId = (int)$args[1];
            $this->campaign = CampaignModel::find($this->campaignId);
        }
        $this->chatUser = $this->channel->getChatUser();
        $this->existingCampaign = $channel->campaign;
        if (null === $this->campaignId) {
            $this->error = 'To link a campaign to this channel, use '
                . '`campaign <campaignId>`.';
            return;
        }

        if (null !== $this->existingCampaign) {
            $this->error = \sprintf(
                'This channel is already registered for "%s".',
                $this->existingCampaign->name
            );
            return;
        }

        if (null === $this->chatUser) {
            $this->error = \sprintf(
                'You must have already created an account on %s (%s) and '
                    . 'linked it to this server before you can register a '
                    . 'channel to a campaign.',
                config('app.name'),
                config('app.url'),
            );
            return;
        }

        if (null === $this->campaign) {
            $this->error = \sprintf(
                'No campaign was found for ID "%d".',
                $this->campaignId
            );
            return;
        }

        if (
            null !== $this->channel->system
            && $this->channel->system !== $this->campaign->system
        ) {
            $this->error = \sprintf(
                'The channel is already registered to play %s. "%s" is playing '
                    . '%s.',
                $this->channel->getSystem(),
                $this->campaign->name,
                $this->campaign->getSystem()
            );
            return;
        }

        if (!$this->userCanLink()) {
            $this->error = 'You must have created the campaign or be the GM '
                . 'to link a Slack channel.';
            return;
        }

        $this->linkCampaignToChannel();

        $this->message = \sprintf(
            '%s has registered this channel for the "%s" campaign, playing %s.',
            $this->channel->username,
            // @phpstan-ignore-next-line
            $this->campaign->name,
            // @phpstan-ignore-next-line
            $this->campaign->getSystem()
        );
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->message;
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->message;
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $attachment = new TextAttachment(
            'Registered',
            $this->message,
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

    protected function linkCampaignToChannel(): void
    {
        $new = false;
        if (null === $this->channel->registered_by) {
            // Brand new channel registration.
            if (Channel::TYPE_SLACK === $this->channel->type) {
                $this->channel->server_name = $this->channel->getSlackTeamName(
                    $this->channel->server_id
                );
                $this->channel->channel_name = $this->channel->getSlackChannelName(
                    $this->channel->channel_id
                );
            }
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
     */
    protected function userCanLink(): bool
    {
        return optional($this->campaign)->registered_by === optional($this->chatUser)->user->id
            || optional($this->campaign)->gm === optional($this->chatUser)->user->id;
    }
}
