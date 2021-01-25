<?php

declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

/**
 * Slack response class.
 */
class SlackResponse extends JsonResponse
{
    public const COLOR_DANGER = 'danger';
    public const COLOR_INFO = '#439Fe0';
    public const COLOR_SUCCESS = 'success';
    public const COLOR_WARNING = 'warning';

    /**
     * Array of attachments to include in the response.
     * @var array<int, array<string, mixed>>
     */
    protected array $attachments = [];

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
     * Whether to delete the original message this is in response to.
     * @var bool
     */
    protected bool $deleteOriginal = false;

    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     */
    public function __construct(
        string $content = '',
        int $status = 200,
        array $headers = []
    ) {
        parent::__construct($content, $status, $headers);
        $this->updateData();
    }

    /**
     * Add an attachment to the output.
     * @param string $title
     * @param string $text
     * @param string $color
     * @return SlackResponse
     */
    public function addAttachment(
        string $title,
        string $text,
        string $color
    ): SlackResponse {
        $this->attachments[] = [
            'color' => $color,
            'text' => $text,
            'title' => $title,
        ];
        $this->updateData();
        return $this;
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

    /**
     * Return the response as a string.
     * @return string
     */
    public function __toString(): string
    {
        return $this->data;
    }
}
