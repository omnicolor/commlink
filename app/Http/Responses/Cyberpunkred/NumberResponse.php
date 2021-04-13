<?php

declare(strict_types=1);

namespace App\Http\Responses\Cyberpunkred;

use App\Events\RollEvent;
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
     * Whether the roll was a one.
     * @var bool
     */
    protected bool $critFailure = false;

    /**
     * Whether the roll was a ten.
     * @var bool
     */
    protected bool $critSuccess = false;

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
        $this->name = $channel->username ?? $channel->user ?? '';

        $this->roll();
        RollEvent::dispatch(
            $this->formatTitle(),
            $this->formatBody(),
            $this->dice,
            $this->channel
        );
        $this->addAttachment($this->buildAttachment())->sendToChannel();
    }

    /**
     * Roll the dice and calculate the result with critical successes and
     * failures.
     */
    protected function roll(): void
    {
        $this->dice = [
            random_int(1, 10),
        ];
        if (1 === $this->dice[0]) {
            // Critical failure.
            $this->critFailure = true;
            $this->dice[] = -1 * random_int(1, 10);
        } elseif (10 === $this->dice[0]) {
            // Critical success.
            $this->critSuccess = true;
            $this->dice[] = random_int(1, 10);
        }

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
        if (!$this->critFailure && !$this->critSuccess) {
            return sprintf('%s made a roll%s', $this->name, $for);
        }

        if ($this->critFailure) {
            return sprintf(
                '%s made a roll with a critical failure%s',
                $this->name,
                $for
            );
        }

        return sprintf(
            '%s made a roll with a critical success%s',
            $this->name,
            $for
        );
    }

    /**
     * Format the body of the Slack message and event.
     * @return string
     */
    protected function formatBody(): string
    {
        if (!$this->critFailure && !$this->critSuccess) {
            return sprintf(
                '1d10 + %1$d = %2$d + %1$d = %3$d',
                $this->addition,
                $this->dice[0],
                $this->result
            );
        }
        if ($this->critFailure) {
            return sprintf(
                '1d10 + %1$d = 1 - %2$d + %1$d = %3$d',
                $this->addition,
                abs($this->dice[1]),
                $this->result
            );
        }
        return sprintf(
            '1d10 + %1$d = 10 + %2$d + %1$d = %3$d',
            $this->addition,
            $this->dice[1],
            $this->result
        );
    }

    /**
     * Create the attachment to send to the channel.
     * @return TextAttachment
     */
    protected function buildAttachment(): TextAttachment
    {
        $color = TextAttachment::COLOR_INFO;
        if ($this->critFailure) {
            $color = TextAttachment::COLOR_DANGER;
        } elseif ($this->critSuccess) {
            $color = TextAttachment::COLOR_SUCCESS;
        }
        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatBody(),
            $color
        );
        $attachment->addFooter(implode(' ', $this->dice));
        return $attachment;
    }
}
