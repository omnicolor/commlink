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
    /**
     * Number of dice to roll.
     * @var int
     */
    protected int $dice = 0;

    protected int $result;
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
                'Rolled %d dice for a result of %d',
                $this->dice,
                $this->result,
            ),
            TextAttachment::COLOR_INFO,
        );
        $attachment->addFooter(\implode(' ', $this->rolls));
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
     * @return string
     */
    public function forDiscord(): string
    {
        return \sprintf('**%s rolled %d**', $this->username, $this->result)
            . \PHP_EOL
            . \sprintf(
                'Rolled %d dice for a result of %d',
                $this->dice,
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
        \sort($this->rolls);
        $this->result = \array_sum(array_slice($this->rolls, 0, 3));
    }
}
