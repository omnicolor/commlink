<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function explode;
use function is_numeric;
use function sprintf;
use function trim;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Number extends Roll
{
    protected const MAX_DICE = 20;
    protected const SUCCESS = 6;
    protected const PANIC = 1;

    protected int $dice = 0;
    protected ?string $error = null;
    /** @var array<int, int> */
    protected array $rolls = [];
    protected int $successes = 0;
    protected int $stress = 0;
    protected int $panics = 0;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', trim($content));
        $this->dice = (int)array_shift($args);
        if ($this->dice > self::MAX_DICE) {
            $this->error = sprintf(
                'You can\'t roll more than %d dice!',
                self::MAX_DICE,
            );
            return;
        }
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->stress = (int)array_shift($args);
        }
        $this->description = trim(implode(' ', $args));
        if ('' !== $this->description) {
            $this->description = ' for "' . $this->description . '"';
        }

        $this->roll();
    }

    protected function formatTitle(): string
    {
        if (0 === $this->successes && 0 !== $this->panics) {
            return sprintf(
                '%s failed with %d %s and panics%s',
                $this->username,
                $this->dice + $this->stress,
                1 === $this->dice ? 'die' : 'dice',
                $this->description,
            );
        }

        if (0 === $this->successes) {
            return sprintf(
                '%s failed with %d %s%s',
                $this->username,
                $this->dice + $this->stress,
                1 === $this->dice ? 'die' : 'dice',
                $this->description,
            );
        }

        if (0 !== $this->panics) {
            return sprintf(
                '%s succeeded, but panics with %d %s%s',
                $this->username,
                $this->dice + $this->stress,
                1 === $this->dice ? 'die' : 'dice',
                $this->description,
            );
        }

        return sprintf(
            '%s succeeded with %d %s%s',
            $this->username,
            $this->dice + $this->stress,
            1 === $this->dice ? 'die' : 'dice',
            $this->description,
        );
    }

    protected function formatText(): string
    {
        return sprintf(
            'Rolled %d success%s',
            $this->successes,
            1 === $this->successes ? '' : 'es',
        );
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('**%s**', $this->formatTitle()) . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->formatTitle() . PHP_EOL
            . $this->formatText() . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $color = 0 === $this->successes
            ? TextAttachment::COLOR_DANGER
            : TextAttachment::COLOR_SUCCESS;

        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatText(),
            $color,
        );
        $attachment->addFooter(implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            /** @psalm-suppress UndefinedClass */
            $this->rolls[] = $roll = DiceService::rollOne(6);
            if (self::SUCCESS === $roll) {
                $this->successes++;
            }
        }
        for ($i = 0; $i < $this->stress; $i++) {
            /** @psalm-suppress UndefinedClass */
            $this->rolls[] = $roll = DiceService::rollOne(6);
            if (self::SUCCESS === $roll) {
                $this->successes++;
            } elseif (self::PANIC === $roll) {
                $this->panics++;
            }
        }
    }
}
