<?php

declare(strict_types=1);

namespace App\Rolls\Subversion;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

class Number extends Roll
{
    protected int $dice = 0;

    /**
     * Number of "Dulled" conditions to apply.
     *
     * If a character tries to roll fewer than three dice, they roll three, but
     * for each die less than three, gain a dulled condition. Each dulled
     * condition reduces the highest value that can be rolled.
     */
    protected int $dulled = 6;

    protected int $result = 0;

    /**
     * Array of individual dice rolls.
     * @var array<int, int>
     */
    protected array $rolls = [];

    public function __construct(
        string $content,
        string $character,
        Channel $channel,
        public ?DiscordMessageReceived $event = null
    ) {
        parent::__construct($content, $character, $channel);

        $args = \explode(' ', \trim($content));
        $this->dice = (int)\array_shift($args);
        while ($this->dice < 3) {
            $this->dice++;
            $this->dulled--;
        }
        $this->roll();
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        $attachment = new TextAttachment(
            \sprintf('%s rolled %d', $this->username, $this->result),
            \sprintf(
                'Rolled %d %sdice: %d + %d + %d = %d',
                $this->dice,
                6 !== $this->dulled ? 'dulled (' . $this->dulled . ') ' : '',
                min($this->dulled, $this->rolls[0]),
                min($this->dulled, $this->rolls[1]),
                min($this->dulled, $this->rolls[2]),
                $this->result,
            ),
            TextAttachment::COLOR_INFO,
        );
        $attachment->addFooter(\implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        return \sprintf('**%s rolled %d**', $this->username, $this->result)
            . \PHP_EOL
            . \sprintf(
                'Rolled %d %sdice: %d + %d + %d = %d',
                $this->dice,
                6 !== $this->dulled ? 'dulled (' . $this->dulled . ') ' : '',
                min($this->dulled, $this->rolls[0]),
                min($this->dulled, $this->rolls[1]),
                min($this->dulled, $this->rolls[2]),
                $this->result,
            ) . \PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    /**
     * Roll the requested number of dice.
     */
    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            $this->rolls[] = random_int(1, 6);
        }
        \rsort($this->rolls);

        if (6 > $this->dulled) {
            foreach (array_slice($this->rolls, 0, 3) as $roll) {
                $this->result += min($roll, $this->dulled);
            }
        } else {
            $this->result = \array_sum(array_slice($this->rolls, 0, 3));
        }
    }
}
