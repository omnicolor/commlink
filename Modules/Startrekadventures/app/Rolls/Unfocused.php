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
use function is_numeric;
use function sprintf;

use const PHP_EOL;

/**
 * Handle a user trying to accomplish a task without an appropriate focus.
 */
class Unfocused extends Roll
{
    protected int $attribute;
    protected int $complications = 0;
    protected int $difficulty;
    protected int $discipline;
    protected int $extraDice = 0;
    protected int $successes = 0;
    protected int $target;

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

        $this->attribute = (int)array_shift($args);
        $this->discipline = (int)array_shift($args);
        $this->target = $this->attribute + $this->discipline;
        $this->difficulty = (int)array_shift($args);
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->extraDice = (int)array_shift($args);
        }
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
            $this->successes < $this->difficulty
                ? TextAttachment::COLOR_DANGER
                : TextAttachment::COLOR_SUCCESS,
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
        if ($this->successes >= $this->difficulty) {
            return sprintf(
                '%s succeeded without a focus%s',
                $this->username,
                $for
            );
        }
        return sprintf(
            '%s failed a roll without a focus%s',
            $this->username,
            $for
        );
    }

    protected function formatBody(): string
    {
        $complications = '';
        if (0 !== $this->complications) {
            $complications = sprintf(
                ' with %d complication%s',
                $this->complications,
                1 !== $this->complications ? 's' : ''
            );
        }
        return sprintf(
            'Rolled %d success%s%s',
            $this->successes,
            1 === $this->successes ? '' : 'es',
            $complications
        );
    }

    protected function roll(): void
    {
        for ($i = 2 + $this->extraDice; 0 < $i; --$i) {
            $roll = DiceService::rollOne(20);
            $this->dice[] = $roll;
            if ($roll <= $this->target) {
                ++$this->successes;
            }
            if (1 === $roll) {
                ++$this->successes;
            } elseif (20 === $roll) {
                ++$this->complications;
            }
        }
    }
}
