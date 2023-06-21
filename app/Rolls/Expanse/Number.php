<?php

declare(strict_types=1);

namespace App\Rolls\Expanse;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

class Number extends Roll
{
    /**
     * Amount to add (or subtract) from the result.
     */
    protected int $addition;

    /**
     * Individual results of the dice rolls.
     * @var array<int, int>
     */
    protected array $dice;

    /**
     * Sum of the rolls + the addition.
     */
    protected int $result;

    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        parent::__construct($content, $character, $channel);
        $args = \explode(' ', $content);
        $this->addition = (int)\array_shift($args);
        $this->description = \implode(' ', $args);
        $this->roll();
        $this->title = $this->formatTitle();
        $this->text = $this->formatText();
        $this->footer = $this->formatFooter();
    }

    public function forSlack(): SlackResponse
    {
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            TextAttachment::COLOR_SUCCESS
        );
        $attachment->addFooter($this->footer);
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        return $response->addAttachment($attachment)->sendToChannel();
    }

    public function forDiscord(): string
    {
        return \sprintf('**%s**', $this->title) . \PHP_EOL . $this->text;
    }

    public function forIrc(): string
    {
        return $this->title . \PHP_EOL . $this->text;
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
        $this->result = \array_sum($this->dice) + $this->addition;
    }

    /**
     * Format the title.
     */
    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = \sprintf(' for "%s"', $this->description);
        }
        return \sprintf('%s made a roll%s', $this->username, $for);
    }

    /**
     * Format the body of the message.
     */
    protected function formatText(): string
    {
        $result = (string)$this->result;
        if (0 !== $this->getStuntPoints()) {
            $result = \sprintf(
                '%d (%d SP)',
                $this->result,
                $this->getStuntPoints()
            );
        }
        return $result;
    }

    /**
     * Format the footer for Slack.
     */
    protected function formatFooter(): string
    {
        return \sprintf(
            '%d %d `%d`',
            $this->dice[0],
            $this->dice[1],
            $this->dice[2]
        );
    }

    /**
     * Figure out how many (if any) stunt points a roll generated.
     */
    protected function getStuntPoints(): int
    {
        // Count number of distinct values rolled to see if there are any that
        // are the same.
        $values = \array_count_values($this->dice);
        if (3 === \count($values)) {
            // No doubles, no stunt points.
            return 0;
        }
        return $this->dice[2];
    }
}
