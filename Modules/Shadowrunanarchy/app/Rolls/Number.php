<?php

declare(strict_types=1);

namespace Modules\Shadowrunanarchy\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function explode;
use function implode;
use function sprintf;
use function trim;

use const PHP_EOL;

class Number extends Roll
{
    protected const int MAX_DICE = 100;
    protected const int MIN_SUCCESS = 5;

    protected int $dice;
    protected ?string $error = null;
    protected bool $glitch = false;
    protected int $successes = 0;
    /** @var array<int, int> */
    protected array $rolls = [];

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

        if (isset($args[0]) && 'glitch' === $args[0]) {
            $this->glitch = true;
            array_shift($args);
        }

        $this->description = trim(implode(' ', $args));
        if ('' !== $this->description) {
            $this->description = ' for "' . $this->description . '"';
        }

        $this->roll();
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return sprintf('**%s**', $this->title) . PHP_EOL
            . $this->text . PHP_EOL
            . 'Rolls: ' . implode(' ', $this->rolls);
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        return $this->title . PHP_EOL
            . $this->text . PHP_EOL
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
            $this->title,
            $this->text,
            $color,
        );
        $attachment->addFooter(implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            $this->rolls[] = $roll = DiceService::rollOne(6);
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
            }
        }
        if ($this->glitch) {
            $this->rolls[] = $roll = DiceService::rollOne(6);
            $glitchResult = '';
            if (1 === $roll) {
                $glitchResult = ', GLITCHED';
            } elseif (5 <= $roll) {
                $glitchResult = ', EXPLOITED';
            }
            $this->title = sprintf(
                '%s rolled %d %s%s%s',
                $this->username,
                $this->successes,
                1 === $this->successes ? 'success' : 'successes',
                $glitchResult,
                $this->description,
            );
            $this->text = sprintf(
                '%d %s plus a glitch die',
                $this->dice,
                1 === $this->dice ? 'die' : 'dice',
            );
            return;
        }
        $this->title = sprintf(
            '%s rolled %d %s%s',
            $this->username,
            $this->successes,
            1 === $this->successes ? 'success' : 'successes',
            $this->description,
        );
        $this->text = sprintf(
            '%d %s',
            $this->dice,
            1 === $this->dice ? 'die' : 'dice',
        );
    }
}
