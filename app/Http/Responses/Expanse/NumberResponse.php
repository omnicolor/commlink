<?php

declare(strict_types=1);

namespace App\Http\Responses\Expanse;

use App\Exceptions\SlackException;
use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Handle a user rolling a generic roll.
 */
class NumberResponse extends SlackResponse
{
    /**
     * Amount to add (or subtract) from the result.
     * @var int
     */
    protected int $addition;

    /**
     * Optional description of what the roll is for.
     * @var string
     */
    protected string $description;

    /**
     * Individual results of the dice rolls.
     * @var array<int, int>
     */
    protected array $dice;

    /**
     * Name to attribute the roll to.
     * @var string
     */
    protected string $name;

    /**
     * Sum of the rolls + the addition.
     * @var int
     */
    protected int $result;

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
        ?Channel $channel = null
    ) {
        if (is_null($channel)) {
            throw new SlackException('Channel is required');
        }

        parent::__construct('', $status, $headers, $channel);
        $args = explode(' ', $content);
        $this->addition = (int)array_shift($args);
        $this->description = implode(' ', $args);
        $this->name = $channel->username;

        $this->roll();
        $this->addAttachment($this->buildAttachment())->sendToChannel();
    }

    /**
     * Figure out how many (if any) stunt points a roll generated.
     * @return int
     */
    protected function getStuntPoints(): int
    {
        $values = array_count_values($this->dice);
        if (count($values) === 3) {
            // No doubles, no stunt points.
            return 0;
        }
        return $this->dice[2];
    }

    /**
     * Roll the dice and calculate the result and stunt points.
     */
    protected function roll(): void
    {
        $this->dice = [
            random_int(1, 6),
            random_int(1, 6),
            random_int(1, 6),
        ];
        $this->result = array_sum($this->dice) + $this->addition;
    }

    /**
     * Format the title for Slack and Event.
     * @return string
     */
    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = sprintf(' for "%s"', $this->description);
        }
        return sprintf('%s made a roll%s', $this->name, $for);
    }

    /**
     * Format the body of the Slack message and event.
     * @return string
     */
    protected function formatBody(): string
    {
        $result = (string)$this->result;
        if (0 !== $this->getStuntPoints()) {
            $result = sprintf(
                '%d (%d SP)',
                $this->result,
                $this->getStuntPoints()
            );
        }
        return $result;
    }

    /**
     * Create the attachment to send to the channel.
     * @return TextAttachment
     */
    protected function buildAttachment(): TextAttachment
    {
        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatBody(),
            TextAttachment::COLOR_INFO
        );
        $attachment->addFooter(sprintf(
            '%d %d `%d`',
            $this->dice[0],
            $this->dice[1],
            $this->dice[2]
        ));
        return $attachment;
    }
}
