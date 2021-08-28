<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Exceptions\SlackException;
use App\Models\Channel;
use App\Models\ChatUser;
use App\Models\Slack\Attachment;
use App\Models\Slack\TextAttachment;
use Illuminate\Http\JsonResponse;

/**
 * Slack response class.
 */
class SlackResponse extends JsonResponse
{
    public const COLOR_DANGER = 'danger';
    public const COLOR_INFO = '#439Fe0';
    public const COLOR_SUCCESS = 'good';
    public const COLOR_WARNING = 'warning';

    /**
     * Array of attachments to include in the response.
     * @var array<int, array<string, mixed>>
     */
    protected array $attachments = [];

    /**
     * Slack channel the request.
     * @var Channel
     */
    protected Channel $channel;

    /**
     * Link between Slack and Commlink.
     * @var ?ChatUser
     */
    protected ?ChatUser $chatUser;

    /**
     * Whether to delete the original message this is in response to.
     * @var bool
     */
    protected bool $deleteOriginal = false;

    /**
     * Optional text to send.
     * @var string
     */
    protected ?string $text = null;

    /**
     * Whether to also send the request to the channel it was requested in.
     * @var bool
     */
    protected bool $toChannel = false;

    /**
     * Whether to set the replace_original property on the response.
     * @var bool
     */
    protected bool $replaceOriginal = false;

    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers);
        $this->channel = $channel ?? new Channel();
        $this->chatUser = $this->channel->getChatUser();
        $this->updateData();
    }

    /**
     * Return the response as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Add an attachment to the output.
     * @param Attachment $attachment
     * @return SlackResponse
     */
    public function addAttachment(Attachment $attachment): SlackResponse
    {
        $this->attachments[] = $attachment->toArray();
        $this->updateData();
        return $this;
    }

    /**
     * Require the user to have a Commlink account and link it to the Slack
     * team before they can do whatever they're trying to do.
     * @param ?ChatUser $chatUser
     * @throws SlackException
     */
    protected function requireCommlink(?ChatUser $chatUser): void
    {
        if (null !== $chatUser) {
            return;
        }
        throw new SlackException(\sprintf(
            'You must have already created an account on <%s|%s> and '
                . 'linked it to this server before you can register a '
                . 'channel to a specific system.',
            config('app.url'),
            config('app.name'),
        ));
    }

    /**
     * Add text to the Slack response, displayed above the attachments, if any.
     * @param string $text
     * @return SlackResponse
     */
    public function setText(string $text): SlackResponse
    {
        $this->text = $text;
        $this->updateData();
        return $this;
    }

    /**
     * Send the response to the channel for everyone to see.
     * @return SlackResponse
     */
    public function sendToChannel(): SlackResponse
    {
        $this->toChannel = true;
        $this->updateData();
        return $this;
    }

    /**
     * This response should replace the original response.
     * @return SlackResponse
     */
    public function replaceOriginal(): SlackResponse
    {
        $this->replaceOriginal = true;
        $this->updateData();
        return $this;
    }

    /**
     * This response should delete the original response.
     * @return SlackResponse
     */
    public function deleteOriginal(): SlackResponse
    {
        $this->deleteOriginal = true;
        $this->updateData();
        return $this;
    }

    /**
     * Update the response's internal representation of the data.
     */
    protected function updateData(): void
    {
        $data = [];
        if ($this->toChannel) {
            $data['response_type'] = 'in_channel';
        } else {
            $data['response_type'] = 'ephemeral';
        }

        if (0 !== \count($this->attachments)) {
            $data['attachments'] = $this->attachments;
        }
        if (null !== $this->text) {
            $data['text'] = $this->text;
        }
        if ($this->replaceOriginal) {
            $data['replace_original'] = true;
        }
        if ($this->deleteOriginal) {
            $data['delete_original'] = true;
        }

        $this->setData($data);
    }

    /**
     * Add help for user if they haven't linked their Commlink user yet.
     */
    protected function addHelpForUnlinkedUser(): void
    {
        $this->addAttachment(new TextAttachment(
            'Note for unregistered users:',
            \sprintf(
                'Your Slack user has not been linked with a %s user. '
                    . 'Go to the <%s/settings|settings page> and copy the '
                    . 'command listed there for this server. If the server '
                    . 'isn\'t listed, follow the instructions there to add '
                    . 'it. You\'ll need to know your server ID (`%s`) and '
                    . 'your user ID (`%s`).',
                config('app.name'),
                config('app.url'),
                $this->channel->server_id,
                $this->channel->user
            ),
            TextAttachment::COLOR_DANGER
        ));
    }

    /**
     * Add help for a channel that is registered for a system, but not
     * a campaign.
     */
    protected function addHelpForUnlinkedCampaign(): void
    {
        $user = optional($this->chatUser)->user;
        if (null === $user) {
            return;
        }

        $campaigns = $user->campaignsRegistered->merge($user->campaignsGmed)
            ->unique();
        if (null !== $this->channel->system) {
            $campaigns = $campaigns->where('system', $this->channel->system);
        }
        if (0 === count($campaigns)) {
            return;
        }
        $campaignList = [];
        if (null === $this->channel->system) {
            foreach ($campaigns as $campaign) {
                $campaignList[] = sprintf(
                    '· %d - %s',
                    $campaign->id,
                    $campaign->name
                );
            }
        } else {
            foreach ($campaigns as $campaign) {
                $campaignList[] = sprintf(
                    '· %d - %s (%s)',
                    $campaign->id,
                    $campaign->name,
                    $campaign->getSystem()
                );
            }
        }
        $this->addAttachment(new TextAttachment(
            'No linked campaign',
            'It doesn\'t look like you\'ve linked a campaign here. Type '
                . '`/roll campaign <campaignId>` to connect your campaign '
                . 'here. Your campaigns:' . \PHP_EOL
                . implode(\PHP_EOL, $campaignList),
            TextAttachment::COLOR_INFO
        ));
    }
}
