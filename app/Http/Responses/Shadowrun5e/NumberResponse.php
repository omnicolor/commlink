<?php

declare(strict_types=1);

namespace App\Http\Responses\Shadowrun5e;

use App\Events\RollEvent;
use App\Exceptions\SlackException;
use App\Http\Responses\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;

/**
 * Respond to a user requesting a number roll.
 */
class NumberResponse extends SlackResponse
{
    /**
     * Optional description the user added for the roll.
     * @var string
     */
    protected string $description = '';

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
        $this->dice = (int)array_shift($args);
        if ($this->dice > 100) {
            throw new SlackException('You can\'t roll more than 100 dice');
        }
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->limit = (int)array_shift($args);
        }
        $this->description = implode(' ', $args);
        $this->name = $channel->username;

        $this->roll();

        if ($this->isCriticalGlitch()) {
            $attachment = $this->formatCriticalGlitch();
        } else {
            $attachment = $this->formatRoll();
        }
        $footer = implode(' ', $this->prettifyRolls());
        if (!is_null($this->limit)) {
            $footer .= sprintf(', limit: %d', $this->limit);
        }
        $attachment->addFooter($footer);
        $this->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Format an attachment to show the user crit glitched.
     * @return TextAttachment
     */
    protected function formatCriticalGlitch(): TextAttachment
    {
        $title = sprintf(
            '%s rolled a critical glitch on %d dice!',
            $this->name,
            $this->dice
        );
        $text = sprintf(
            'Rolled %d ones with no successes%s!',
            $this->fails,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
        RollEvent::dispatch($title, $text, $this->rolls, $this->channel);
        return new TextAttachment($title, $text, TextAttachment::COLOR_DANGER);
    }

    /**
     * Format an attachment to show the roll results.
     * @return TextAttachment
     */
    protected function formatRoll(): TextAttachment
    {
        $color = TextAttachment::COLOR_SUCCESS;
        if ($this->isGlitch() || 0 === $this->successes) {
            $color = TextAttachment::COLOR_DANGER;
        }

        if (!is_null($this->limit) && $this->limit < $this->successes) {
            $text = sprintf(
                'Rolled %d successes%s, hit limit',
                $this->limit,
                ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
            );
            RollEvent::dispatch(
                $this->formatTitle(),
                $text,
                $this->rolls,
                $this->channel
            );
            return new TextAttachment($this->formatTitle(), $text, $color);
        }

        $text = sprintf(
            'Rolled %d successes%s',
            $this->successes,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
        return new TextAttachment($this->formatTitle(), $text, $color);
    }

    /**
     * Format the title part of the roll.
     * @return string
     */
    protected function formatTitle(): string
    {
        return sprintf(
            '%s rolled %d %s%s%s',
            $this->name,
            $this->dice,
            $this->dice === 1 ? 'die' : 'dice',
            !is_null($this->limit) ? sprintf(' with a limit of %d', $this->limit) : '',
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
        rsort($this->rolls, SORT_NUMERIC);
    }

    /**
     * Bold successes, strike out failures in the roll list.
     * @return array<int, string>
     */
    protected function prettifyRolls(): array
    {
        $rolls = $this->rolls;
        array_walk($rolls, function (int &$value, int $key): void {
            if ($value >= 5) {
                $value = sprintf('*%d*', $value);
            } elseif ($value == 1) {
                $value = sprintf('~%d~', $value);
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
        return $this->fails > floor($this->dice / 2);
    }

    /**
     * Return whether the roll was a critical glitch.
     * @return bool
     */
    protected function isCriticalGlitch(): bool
    {
        return $this->isGlitch() && 0 === $this->successes;
    }
}
