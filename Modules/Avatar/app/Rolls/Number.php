<?php

declare(strict_types=1);

namespace Modules\Avatar\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function abs;
use function array_shift;
use function array_sum;
use function explode;
use function implode;
use function max;
use function min;
use function sprintf;
use function trim;

use const PHP_EOL;

class Number extends Roll
{
    protected const int MAX_BONUS = 4;
    protected const int MAX_PENALTY = -3;
    protected const int SUCCESS = 10;
    protected const int FAILURE = 6;

    protected int $modifier;
    protected int $result;
    /** @var array<int, int> */
    protected array $rolls = [];

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', $content);
        $this->modifier = (int)array_shift($args);
        $this->modifier = max($this->modifier, self::MAX_PENALTY);
        $this->modifier = min($this->modifier, self::MAX_BONUS);

        $this->description = trim(implode(' ', $args));
        if ('' !== $this->description) {
            $this->description = sprintf(' for "%s"', $this->description);
        }

        $this->roll();
    }

    #[Override]
    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL . $this->text;
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->title . PHP_EOL . $this->text;
    }

    #[Override]
    public function forSlack(): Response
    {
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            self::FAILURE < $this->result
                ? TextAttachment::COLOR_SUCCESS
                : TextAttachment::COLOR_DANGER,
        );
        // @phpstan-ignore method.deprecated
        return (new Response())
            ->addAttachment($attachment)
            ->sendToChannel();
    }

    protected function roll(): void
    {
        $this->rolls[] = DiceService::rollOne(6);
        $this->rolls[] = DiceService::rollOne(6);

        $this->result = array_sum($this->rolls) + $this->modifier;
        if (0 === $this->modifier) {
            $explanation = sprintf('%d+%d', $this->rolls[0], $this->rolls[1]);
        } elseif (0 < $this->modifier) {
            $explanation = sprintf(
                '%d+%d+%d',
                $this->rolls[0],
                $this->rolls[1],
                $this->modifier,
            );
        } else {
            $explanation = sprintf(
                '%d+%d-%d',
                $this->rolls[0],
                $this->rolls[1],
                abs($this->modifier),
            );
        }
        $this->title = sprintf(
            match (true) {
                self::FAILURE >= $this->result => '%s missed%s!',
                self::SUCCESS <= $this->result => '%s hit%s!',
                default => '%s weakly hit%s!',
            },
            $this->username,
            $this->description,
        );
        $this->text = sprintf(
            'Rolled %d (%s)',
            $this->result,
            $explanation,
        );
    }
}
