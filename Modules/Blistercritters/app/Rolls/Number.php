<?php

declare(strict_types=1);

namespace Modules\Blistercritters\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function count;
use function explode;
use function is_numeric;
use function max;
use function min;
use function sprintf;
use function trim;

use const PHP_EOL;

class Number extends Roll
{
    protected const string TYPE_ADVANTAGE = 'advantage';
    protected const string TYPE_DISADVANTAGE = 'disadvantage';
    protected const string TYPE_STANDARD = 'standard';

    protected int $die;
    protected ?string $error = null;
    protected int $result;
    /** @var array<int, int> */
    protected array $rolls = [];
    protected int $target;
    protected string $type = self::TYPE_STANDARD;

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', trim($content));

        if (
            2 > count($args)
            || !is_numeric($args[0] ?? null)
            || !is_numeric($args[1] ?? null)
        ) {
            $this->error = 'You must include the die size and the target number';
            return;
        }

        $this->die = (int)array_shift($args);
        $this->target = (int)array_shift($args);

        if (isset($args[0]) && ('adv' === $args[0])) {
            $this->type = self::TYPE_ADVANTAGE;
            array_shift($args);
        } elseif (isset($args[0]) && ('dis' === $args[0])) {
            $this->type = self::TYPE_DISADVANTAGE;
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

        $color = $this->result < $this->target
            ? TextAttachment::COLOR_DANGER
            : TextAttachment::COLOR_SUCCESS;

        $attachment = new TextAttachment(
            $this->formatTitle(),
            $this->formatText(),
            $color,
        );
        $attachment->addFooter('Rolls: ' . implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    protected function formatTitle(): string
    {
        if (1 === $this->result) {
            return sprintf(
                '%s botched the roll%s',
                $this->username,
                $this->description,
            );
        }
        if ($this->result >= $this->target) {
            return sprintf(
                '%s succeeded%s',
                $this->username,
                $this->description,
            );
        }
        return sprintf('%s failed%s', $this->username, $this->description);
    }

    protected function formatText(): string
    {
        $type = '';
        if (self::TYPE_STANDARD !== $this->type) {
            $type = ' (' . $this->type . ')';
        }
        return sprintf(
            'Rolled %d vs %d%s',
            $this->result,
            $this->target,
            $type,
        );
    }

    protected function roll(): void
    {
        if (self::TYPE_ADVANTAGE === $this->type) {
            $this->rolls[] = DiceService::rollOne($this->die);
            $this->rolls[] = DiceService::rollOne($this->die);
            $this->result = max($this->rolls);
            return;
        }
        if (self::TYPE_DISADVANTAGE === $this->type) {
            $this->rolls[] = DiceService::rollOne($this->die);
            $this->rolls[] = DiceService::rollOne($this->die);
            $this->result = min($this->rolls);
            return;
        }
        $this->result = $this->rolls[] = DiceService::rollOne($this->die);
    }
}
