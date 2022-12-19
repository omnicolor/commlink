<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Shadowrun5e\Character;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;

class Fade extends Number
{
    protected ?string $error = null;

    // @phpstan-ignore-next-line
    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        Roll::__construct($content, $character, $channel);

        if (null === $this->character) {
            $this->error = 'You must have a character linked to make fade '
                . 'tests';
            return;
        }

        /** @var Character */
        $character = $this->character;

        if (null === $character->resonance) {
            $this->error = 'Your character must have a resonance attribute to '
                . 'make fading tests';
            return;
        }

        $this->dice = $character->resonance + $character->willpower;
        $this->roll();
        $this->formatRoll();
    }

    protected function formatTitle(): string
    {
        return \sprintf(
            '%s rolled %d %s for a fading test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
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
