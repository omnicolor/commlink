<?php

declare(strict_types=1);

namespace App\Rolls\Cyberpunkred;

use App\Events\InitiativeAdded;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Initiative;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

/**
 * Handle a user trying to add their initiative.
 */
class Init extends Roll
{
    /**
     * Arguments passed to the roll.
     * @var array<int, string>
     */
    protected array $args = [];

    /**
     * Optional modifier for the character's initiative.
     * @var int
     */
    public int $modifier = 0;

    /**
     * Reflexes used to determine the character's initiative.
     * @var int
     */
    public int $reflexes;

    /**
     * Result of the die roll.
     * @var int
     */
    public int $roll;

    /**
     * Initiative object created for the roll.
     * @var Initiative
     */
    protected Initiative $initiative;

    /**
     * Constructor.
     * @param string $content
     * @param string $username
     * @param Channel $channel
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        $args = \explode(' ', $content);

        // Remove 'init' from argument list.
        \array_shift($args);
        $this->args = $args;
    }

    /**
     * Roll a d10 for initiative, notifying listeners of the results.
     */
    protected function roll(): void
    {
        // Rolls with a character attached shouldn't need to enter reflexes.
        if (isset($this->character)) {
            $this->username = (string)$this->character;
            if (2 === count($this->args)) {
                // Get rid of the character's reflexes.
                \array_shift($this->args);
            }
            // @phpstan-ignore-next-line
            $this->reflexes = $this->character->reflexes;
            if (1 === count($this->args)) {
                $this->modifier = (int)\array_shift($this->args);
            }
        } else {
            if (1 <= count($this->args)) {
                $this->reflexes = (int)\array_shift($this->args);
            }
            if (1 === count($this->args)) {
                $this->modifier = (int)\array_shift($this->args);
            }
        }

        $this->roll = random_int(1, 10);
        $this->initiative = Initiative::updateOrCreate(
            [
                'campaign_id' => optional($this->campaign)->id,
                'channel_id' => $this->channel->channel_id,
                'character_id' => optional($this->channel->character())->id,
                'character_name' => $this->username,
            ],
            ['initiative' => $this->roll + $this->reflexes + $this->modifier],
        );
        if (null !== $this->campaign) {
            InitiativeAdded::dispatch($this->initiative, $this->campaign);
        }
    }

    /**
     * Format the response's body for either Slack or Discord.
     * @return string
     */
    protected function formatBody(): string
    {
        $extra = '';
        if (0 < $this->modifier) {
            $extra = ' + ' . $this->modifier;
        } elseif (0 > $this->modifier) {
            $extra = ' - ' . abs($this->modifier);
        }
        return sprintf(
            'Rolled: %d + %d%s = %d',
            $this->roll,
            $this->reflexes,
            $extra,
            $this->roll + $this->reflexes + $this->modifier,
        );
    }

    /**
     * Return the initiative information formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        if (null === $this->character && 0 === count($this->args)) {
            throw new SlackException(
                'Rolling initiative without a linked character requires your '
                    . 'reflexes, and optionally any modififers: '
                    . '`/roll init 8 -2` for a character with 8 REF and a '
                    . 'wound modifier of -2'
            );
        }
        $this->roll();
        $attachment = new TextAttachment(
            sprintf('Initiative added for %s', $this->username),
            $this->formatBody()
        );
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the initiative response formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        if (null === $this->character && 0 === count($this->args)) {
            return 'Rolling initiative without a linked character requires '
                . 'your reflexes, and optionally any modififers: '
                . '`/roll init 8 -2` for a character with 8 REF and a wound '
                . 'modifier of -2';
        }
        $this->roll();
        return sprintf('**Initiative added for %s**', $this->username)
            . \PHP_EOL . $this->formatBody();
    }
}
