<?php

declare(strict_types=1);

namespace App\Http\Responses\Slack;

use App\Models\Channel;
use App\Models\ChatUser;
use Illuminate\Http\JsonResponse;
use Omnicolor\Slack\Attachment;
use Omnicolor\Slack\Exceptions\SlackException;

use function count;
use function sprintf;

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

    protected ?Channel $channel;

    /**
     * Link between Slack and Commlink.
     */
    protected ?ChatUser $chatUser;

    /**
     * Whether to delete the original message this is in response to.
     */
    protected bool $deleteOriginal = false;

    /**
     * Optional text to send.
     */
    protected ?string $text = null;

    /**
     * Whether to also send the request to the channel it was requested in.
     */
    protected bool $toChannel = false;

    /**
     * Whether to set the replace_original property on the response.
     */
    protected bool $replaceOriginal = false;

    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        string $content = '',
        int $status = self::HTTP_OK,
        array $headers = [],
        ?Channel $channel = null,
    ) {
        parent::__construct($content, $status, $headers);
        $this->channel = $channel ?? new Channel();
        $this->chatUser = $this->channel->getChatUser();
        $this->updateData();
    }

    public function __toString(): string
    {
        return $this->data;
    }

    /**
     * Add an attachment to the output.
     */
    public function addAttachment(Attachment $attachment): SlackResponse
    {
        $this->attachments[] = $attachment->jsonSerialize();
        $this->updateData();
        return $this;
    }

    /**
     * Require the user to have a Commlink account and link it to the Slack
     * team before they can do whatever they're trying to do.
     * @throws SlackException
     */
    protected function requireCommlink(?ChatUser $chatUser): void
    {
        if (null !== $chatUser) {
            return;
        }
        throw new SlackException(sprintf(
            'You must have already created an account on <%s|%s> and '
                . 'linked it to this server before you can register a '
                . 'channel to a specific system.',
            config('app.url'),
            config('app.name'),
        ));
    }

    /**
     * Add text to the Slack response, displayed above the attachments, if any.
     */
    public function setText(string $text): SlackResponse
    {
        $this->text = $text;
        $this->updateData();
        return $this;
    }

    /**
     * Send the response to the channel for everyone to see.
     */
    public function sendToChannel(): SlackResponse
    {
        $this->toChannel = true;
        $this->updateData();
        return $this;
    }

    /**
     * This response should replace the original response.
     */
    public function replaceOriginal(): SlackResponse
    {
        $this->replaceOriginal = true;
        $this->updateData();
        return $this;
    }

    /**
     * This response should delete the original response.
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

        if (0 !== count($this->attachments)) {
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
}
