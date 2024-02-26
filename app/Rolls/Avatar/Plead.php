<?php

declare(strict_types=1);

namespace App\Rolls\Avatar;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

/**
 * Handle a character making a Plead action.
 */
class Plead extends Roll
{
    // Rolling 10 or higher is a success.
    protected const SUCCESS = 10;
    // Rolling 6 or less is a failure. Between that is kinda close to success.
    protected const FAILURE = 6;

    /**
     * Amount to add (or subtract) from the result.
     */
    protected ?int $addition = null;

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
     * Error to return, if needed.
     */
    protected ?string $error = null;

    /**
     * Sum of the rolls +/- the addition.
     */
    protected int $result;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);
        if ('avatar' !== $channel->system) {
            $this->error = 'Avatar moves are only available for channels '
                . 'registered for the Avatar system.';
            return;
        }

        $args = \explode(' ', $content);
        \array_shift($args);
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->addition = (int)\array_shift($args);
        }
        $this->description = \implode(' ', $args);

        $this->roll();
    }

    protected function roll(): void
    {
        $this->dice = DiceService::rollMany(2, 6);
        $this->result = \array_sum($this->dice) + (int)$this->addition;
    }

    protected function formatBody(): string
    {
        if (null === $this->addition) {
            return \sprintf(
                '2d6 = %d + %d = %d',
                $this->dice[0],
                $this->dice[1],
                $this->result
            );
        }
        if (0 < $this->addition) {
            return \sprintf(
                '2d6 + %1$d = %2$d + %3$d + %1$d = %4$d',
                $this->addition,
                $this->dice[0],
                $this->dice[1],
                $this->result
            );
        }
        return \sprintf(
            '2d6 - %1$d = %2$d + %3$d - %1$d = %4$d',
            abs($this->addition),
            $this->dice[0],
            $this->dice[1],
            $this->result
        );
    }

    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = \sprintf(' for "%s"', $this->description);
        }
        if (self::FAILURE >= $this->result) {
            return \sprintf('%s failed a plead roll%s', $this->username, $for);
        }

        if (self::SUCCESS <= $this->result) {
            return \sprintf(
                '%s succeeded in a plead roll%s',
                $this->username,
                $for
            );
        }

        return \sprintf(
            '%s is getting close to succeeding in pleading%s',
            $this->username,
            $for
        );
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return sprintf('**%s**', $this->formatTitle()) . \PHP_EOL
            . $this->formatBody();
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return $this->formatTitle() . \PHP_EOL . $this->formatBody();
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $color = TextAttachment::COLOR_INFO;
        if (self::FAILURE >= $this->result) {
            $color = TextAttachment::COLOR_DANGER;
        } elseif (self::SUCCESS <= $this->result) {
            $color = TextAttachment::COLOR_SUCCESS;
        }

        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatBody(),
            $color
        );

        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }
}
