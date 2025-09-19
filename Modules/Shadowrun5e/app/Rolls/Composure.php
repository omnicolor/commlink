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

/**
 * Roll a Shadowrun 5E composure test.
 */
class Composure extends Number
{
    protected ?string $error = null;

    // @phpstan-ignore constructor.missingParentCall (Calls grandparent)
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
        $this->dice = $this->character->composure;
        $this->roll();
        if ($this->isCriticalGlitch()) {
            $this->formatCriticalGlitch();
        } else {
            $this->formatRoll();
        }
    }

    #[Override]
    protected function formatTitle(): string
    {
        return sprintf(
            '%s rolled %d %s for a composure test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
        );
    }

    #[Override]
    protected function formatCriticalGlitch(): void
    {
        $this->title = sprintf(
            '%s critically glitched on a composure roll!',
            $this->username
        );
        $this->text = sprintf(
            'Rolled %d ones with no successes!',
            $this->fails
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
