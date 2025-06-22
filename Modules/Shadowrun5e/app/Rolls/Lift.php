<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Discord\Builders\MessageBuilder;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Response;
use Override;

use function sprintf;

/**
 * Roll a Shadowrun 5E lift/carry test.
 */
class Lift extends Number
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
            $this->error = 'You must have a character linked to make '
                . 'lift/carry tests';
            return;
        }
        $this->dice = $this->character->lift_carry;
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
            '%s rolled %d %s for a lift/carry test',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice'
        );
    }

    #[Override]
    protected function formatCriticalGlitch(): void
    {
        $this->title = sprintf(
            '%s critically glitched on a lift/carry roll!',
            $this->username
        );
        $this->text = sprintf(
            'Rolled %d ones with no successes!',
            $this->fails
        );
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
            return sprintf(
                '%s, %s',
                $this->username,
                $this->error
            );
        }
        return parent::forIrc();
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        return parent::forSlack();
    }
}
