<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function implode;
use function sprintf;

use const PHP_EOL;

class Fade extends Number
{
    protected ?string $error = null;

    // @phpstan-ignore constructor.missingParentCall (Calls grandparent)
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

        if (null === $this->character->resonance) {
            $this->error = 'Your character must have a resonance attribute to '
                . 'make fading tests';
            return;
        }

        $this->dice = $this->character->resonance + $this->character->willpower;
        $this->roll();
        $this->formatRoll();
    }

    #[Override]
    protected function formatTitle(): string
    {
        return sprintf(
            '%s rolled %d %s for a fading test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
        );
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        return parent::forSlack();
    }

    #[Override]
    public function forDiscord(): string | MessageBuilder
    {
        if (null !== $this->error) {
            return sprintf(
                '%s, %s',
                $this->username,
                $this->error
            );
        }
        return parent::forDiscord();
    }

    #[Override]
    public function forIrc(): string
    {
        if (null !== $this->error) {
            return sprintf('%s, %s', $this->username, $this->error);
        }
        return $this->title . PHP_EOL
            . $this->text . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }
}
