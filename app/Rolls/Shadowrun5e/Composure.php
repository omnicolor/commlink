<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;

/**
 * Roll a Shadowrun 5E composure test.
 */
class Composure extends Number
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
            $this->error = 'You must have a character linked to make Composure '
                . 'tests';
            return;
        }
        // @phpstan-ignore-next-line
        $this->dice = $this->character->composure;
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
            '%s rolled %d %s for a composure test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
        );
    }

    protected function formatCriticalGlitch(): void
    {
        $this->title = \sprintf(
            '%s critically glitched on a composure roll!',
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

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return \sprintf('%s, %s', $this->username, $this->error);
        }

        return $this->title . \PHP_EOL
            . $this->text . \PHP_EOL
            . 'Rolls: ' . \implode(' ', $this->rolls);
    }
}
