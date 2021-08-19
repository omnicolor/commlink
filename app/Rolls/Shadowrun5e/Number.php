<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

/**
 * Roll a Shadowrun number, representing a set of six-sided dice.
 */
class Number extends Roll
{
    /**
     * Number of dice to roll.
     * @var int
     */
    protected int $dice = 0;

    /**
     * Number of failures the roll produced.
     * @var int
     */
    protected int $fails = 0;

    /**
     * Number of successes to keep.
     * @var ?int
     */
    protected ?int $limit = null;

    /**
     * Who's doing the rolling.
     * @var string
     */
    protected string $name;

    /**
     * Array of individual dice rolls.
     * @var array<int, int>
     */
    protected array $rolls = [];

    /**
     * Number of successes the roll produced.
     * @var int
     */
    protected int $successes = 0;

    /**
     * Constructor.
     * @param string $content
     * @param string $character
     */
    public function __construct(string $content, string $character)
    {
        $this->name = $character;
        $args = \explode(' ', $content);
        $this->dice = (int)\array_shift($args);
        if (isset($args[0]) && \is_numeric($args[0])) {
            $this->limit = (int)\array_shift($args);
        }
        $this->description = \implode(' ', $args);

        if ($this->dice > 100) {
            return;
        }
        $this->roll();

        if ($this->isCriticalGlitch()) {
            $this->formatCriticalGlitch();
        } else {
            $this->formatRoll();
        }
    }

    /**
     * Format the text and title for a critical glitch.
     */
    protected function formatCriticalGlitch(): void
    {
        $this->title = \sprintf(
            '%s rolled a critical glitch on %d dice!',
            $this->name,
            $this->dice
        );
        $this->text = \sprintf(
            'Rolled %d ones with no successes%s!',
            $this->fails,
            ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
        );
    }

    /**
     * Format the text and title for a normal roll.
     */
    protected function formatRoll(): void
    {
        $this->title = $this->formatTitle();
        if (null !== $this->limit && $this->limit < $this->successes) {
            $this->text = \sprintf(
                'Rolled %d successes%s, hit limit',
                $this->limit,
                ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
            );
            return;
        }

        $this->text = \sprintf(
            'Rolled %d successes%s',
            $this->successes,
            ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
        );
    }

    /**
     * Format the title part of the roll.
     * @return string
     */
    protected function formatTitle(): string
    {
        return \sprintf(
            '%s rolled %d %s%s%s',
            $this->name,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice',
            null !== $this->limit ? \sprintf(' with a limit of %d', $this->limit) : '',
            $this->isGlitch() ? ', glitched' : ''
        );
    }

    /**
     * Roll the requested number of dice, checking for successes and failures.
     */
    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            $this->rolls[] = $roll = random_int(1, 6);
            if (5 <= $roll) {
                $this->successes++;
            }
            if (1 === $roll) {
                $this->fails++;
            }
        }
        \rsort($this->rolls, \SORT_NUMERIC);
    }

    /**
     * Bold successes, strike out failures in the roll list.
     * @return array<int, string>
     */
    protected function prettifyRolls(): array
    {
        $rolls = $this->rolls;
        \array_walk($rolls, function (int &$value, int $key): void {
            if ($value >= 5) {
                $value = \sprintf('*%d*', $value);
            } elseif (1 == $value) {
                $value = \sprintf('~%d~', $value);
            }
        });
        // @phpstan-ignore-next-line
        return $rolls;
    }

    /**
     * Return whether the roll was a glitch.
     * @return bool
     */
    protected function isGlitch(): bool
    {
        if (0 === $this->fails) {
            // No matter how small the dice pool, no ones means it's not
            // a glitch.
            return false;
        }
        // If half of the dice were ones, it's a glitch.
        return $this->fails > \floor($this->dice / 2);
    }

    /**
     * Return whether the roll was a critical glitch.
     * @return bool
     */
    protected function isCriticalGlitch(): bool
    {
        return $this->isGlitch() && 0 === $this->successes;
    }

    /**
     * Return the roll formatted for Slack.
     * @param Channel $channel
     * @return SlackResponse
     * @throws SlackException
     */
    public function forSlack(Channel $channel): SlackResponse
    {
        if ($this->dice > 100) {
            throw new SlackException('You can\'t roll more than 100 dice');
        }
        $color = TextAttachment::COLOR_SUCCESS;
        if ($this->isCriticalGlitch() || $this->isGlitch()) {
            $color = TextAttachment::COLOR_DANGER;
        }
        $footer = \implode(' ', $this->prettifyRolls());
        if (null !== $this->limit) {
            $footer .= \sprintf(', limit: %d', $this->limit);
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $attachment->addFooter($footer);
        $response = new SlackResponse('', SlackResponse::HTTP_OK, [], $channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        if ($this->dice > 100) {
            return \sprintf(
                '%s, you can\'t roll more than 100 dice!',
                $this->name
            );
        }
        $footer = 'Rolls: ' . \implode(' ', $this->rolls);
        if (null !== $this->limit) {
            $footer .= \sprintf(', Limit: %d', $this->limit);
        }
        return \sprintf('**%s**', $this->formatTitle()) . \PHP_EOL
            . $this->text . \PHP_EOL
            . $footer;
    }
}
