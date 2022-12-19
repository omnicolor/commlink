<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;

/**
 * Roll a Shadowrun 5E lift/carry test.
 */
class Lift extends Number
{
    protected ?string $error = null;

    // @phpstan-ignore-next-line
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        Roll::__construct($content, $username, $channel);

        if (null === $this->character) {
            $this->error = 'You must have a character linked to make '
                . 'lift/carry tests';
            return;
        }
        // @phpstan-ignore-next-line
        $this->dice = $this->character->lift_carry;
        $this->roll();
        if ($this->isCriticalGlitch()) {
            $this->formatCriticalGlitch();
        } else {
            $this->formatRoll();
        }
    }

    protected function formatTitle(): string
    {
        return \sprintf(
            '%s rolled %d %s for a lift/carry test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
        );
    }

    protected function formatCriticalGlitch(): void
    {
        $this->title = \sprintf(
            '%s critically glitched on a lift/carry roll!',
            $this->username
        );
        $this->text = \sprintf(
            'Rolled %d ones with no successes!',
            $this->fails
        );
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        return parent::forSlack();
    }

    public function forDiscord(): string | MessageBuilder
    {
        if (null !== $this->error) {
            return \sprintf(
                '%s, %s',
                $this->username,
                $this->error
            );
        }
        return parent::forDiscord();
    }
}
