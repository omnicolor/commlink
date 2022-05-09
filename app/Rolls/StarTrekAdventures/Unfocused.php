<?php

declare(strict_types=1);

namespace App\Rolls\StarTrekAdventures;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

/**
 * Handle a user trying to accomplish a task without an appropriate focus.
 */
class Unfocused extends Roll
{
    protected int $attribute;
    protected int $complications = 0;
    protected int $difficulty;
    protected int $discipline;
    protected int $extraDice = 0;
    protected int $successes = 0;
    protected int $target;

    /**
     * @var array<int, int>
     */
    protected array $dice;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        $args = \explode(' ', $content);

        // Get rid of the name of the roll.
        array_shift($args);

        $this->attribute = (int)array_shift($args);
        $this->discipline = (int)array_shift($args);
        $this->target = $this->attribute + $this->discipline;
        $this->difficulty = (int)array_shift($args);
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->extraDice = (int)array_shift($args);
        }
        $this->description = implode(' ', $args);

        $this->roll();
    }

    public function forDiscord(): string
    {
        return \sprintf('**%s**', $this->formatTitle()) . \PHP_EOL
            . $this->formatBody() . \PHP_EOL
            . 'Rolls: ' . \implode(' ', $this->dice);
    }

    public function forSlack(): SlackResponse
    {
        $color = TextAttachment::COLOR_SUCCESS;
        if ($this->successes < $this->difficulty) {
            $color = TextAttachment::COLOR_DANGER;
        }
        $footer = 'Rolls: ' . implode(' ', $this->dice);
        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatBody(),
            $color
        );
        $attachment->addFooter($footer);
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        return $response->addAttachment($attachment)->sendToChannel();
    }

    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = \sprintf(' for "%s"', $this->description);
        }
        if ($this->successes >= $this->difficulty) {
            return \sprintf(
                '%s succeeded without a focus%s',
                $this->username,
                $for
            );
        }
        return \sprintf(
            '%s failed a roll without a focus%s',
            $this->username,
            $for
        );
    }

    protected function formatBody(): string
    {
        $complications = '';
        if (0 !== $this->complications) {
            $complications = sprintf(
                ' with %d complication%s',
                $this->complications,
                1 !== $this->complications ? 's' : ''
            );
        }
        return sprintf(
            'Rolled %d success%s%s',
            $this->successes,
            1 === $this->successes ? '' : 'es',
            $complications
        );
    }

    protected function roll(): void
    {
        for ($i = 2 + $this->extraDice; 0 < $i; $i--) {
            $roll = random_int(1, 20);
            $this->dice[] = $roll;
            if ($roll <= $this->target) {
                $this->successes++;
            }
            if (1 === $roll) {
                $this->successes++;
            } elseif (20 === $roll) {
                $this->complications++;
            }
        }
    }
}
