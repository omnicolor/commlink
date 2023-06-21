<?php

declare(strict_types=1);

namespace App\Rolls\Cyberpunkred;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

/**
 * Handle a user rolling a generic roll.
 */
class Number extends Roll
{
    protected const CRIT_FAILURE = 1;
    protected const CRIT_SUCCESS = 10;

    /**
     * Amount to add (or subtract) from the result.
     */
    protected int $addition;

    /**
     * Whether the roll was a one.
     */
    protected bool $critFailure = false;

    /**
     * Whether the roll was a ten.
     */
    protected bool $critSuccess = false;

    /**
     * Optional description of what the roll is for.
     */
    protected string $description;

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
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        $args = \explode(' ', $content);
        $this->addition = (int)\array_shift($args);
        $this->description = \implode(' ', $args);

        $this->roll();
    }

    /**
     * Return the roll formatted for Slack.
     */
    public function forSlack(): SlackResponse
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
        $attachment->addFooter(\implode(' ', $this->dice));

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
     */
    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->formatTitle()) . \PHP_EOL
            . $this->formatBody();
    }

    /**
     * Return the roll formatted for IRC.
     */
    public function forIrc(): string
    {
        return $this->formatTitle() . \PHP_EOL . $this->formatBody();
    }

    /**
     * Format the body of the Slack message and event.
     */
    protected function formatBody(): string
    {
        if (!$this->critFailure && !$this->critSuccess) {
            return \sprintf(
                '1d10 + %1$d = %2$d + %1$d = %3$d',
                $this->addition,
                $this->dice[0],
                $this->result
            );
        }
        if ($this->critFailure) {
            return \sprintf(
                '1d10 + %1$d = 1 - %2$d + %1$d = %3$d',
                $this->addition,
                \abs($this->dice[1]),
                $this->result
            );
        }
        return \sprintf(
            '1d10 + %1$d = 10 + %2$d + %1$d = %3$d',
            $this->addition,
            $this->dice[1],
            $this->result
        );
    }

    /**
     * Format the title for Slack and Event.
     * @return string
     */
    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = \sprintf(' for "%s"', $this->description);
        }
        if (!$this->critFailure && !$this->critSuccess) {
            return \sprintf('%s made a roll%s', $this->username, $for);
        }

        if ($this->critFailure) {
            return \sprintf(
                '%s made a roll with a critical failure%s',
                $this->username,
                $for
            );
        }

        return \sprintf(
            '%s made a roll with a critical success%s',
            $this->username,
            $for
        );
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
        if (self::CRIT_FAILURE === $this->dice[0]) {
            $this->critFailure = true;
            $this->dice[] = -1 * random_int(1, 10);
        } elseif (self::CRIT_SUCCESS === $this->dice[0]) {
            $this->critSuccess = true;
            $this->dice[] = random_int(1, 10);
        }

        $this->result = \array_sum($this->dice) + $this->addition;
    }
}
