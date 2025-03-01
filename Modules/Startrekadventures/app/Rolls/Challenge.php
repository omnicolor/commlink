<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function array_shift;
use function explode;
use function implode;
use function sprintf;

use const PHP_EOL;

class Challenge extends Roll
{
    protected bool $effect = false;
    protected int $number;
    protected int $score = 0;

    /**
     * @var array<int, int>
     */
    protected array $dice;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', $content);

        // Get rid of the name of the roll.
        array_shift($args);

        $this->number = (int)array_shift($args);
        $this->description = implode(' ', $args);

        $this->roll();
    }

    #[Override]
    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->formatTitle()) . PHP_EOL
            . $this->formatBody() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->dice);
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->formatTitle() . PHP_EOL
            . $this->formatBody() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->dice);
    }

    #[Override]
    public function forSlack(): Response
    {
        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatBody(),
        );
        $attachment->addFooter('Rolls: ' . implode(' ', $this->dice));
        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }

    protected function formatTitle(): string
    {
        $for = '';
        if ('' !== $this->description) {
            $for = sprintf(' for "%s"', $this->description);
        }
        return sprintf(
            '%s rolled a score of %d with%s an Effect%s',
            $this->username,
            $this->score,
            $this->effect ? '' : 'out',
            $for
        );
    }

    protected function formatBody(): string
    {
        return sprintf('Rolled %d challenge dice', $this->number);
    }

    protected function roll(): void
    {
        for ($i = 0; $i < $this->number; $i++) {
            $roll = DiceService::rollOne(6);
            $this->dice[] = $roll;
            if (5 === $roll || 6 === $roll) {
                $this->effect = true;
                $this->score++;
                continue;
            }
            if (3 <= $roll) {
                continue;
            }
            $this->score += $roll;
        }
    }
}
