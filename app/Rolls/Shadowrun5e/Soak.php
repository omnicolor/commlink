<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;

/**
 * Roll a Shadowrun 5E soak test.
 */
class Soak extends Number
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
            $this->error = 'You must have a character linked to make soak '
                . 'tests';
            return;
        }

        /** @var \App\Models\Shadowrun5e\Character */
        $character = $this->character;
        $this->dice = $character->soak;

        $this->roll();
        $this->formatRoll();
    }

    protected function formatTitle(): string
    {
        return \sprintf(
            '%s rolled %d %s for a soak test',
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
