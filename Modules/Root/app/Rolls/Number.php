<?php

declare(strict_types=1);

namespace Modules\Root\Rolls;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function abs;
use function array_shift;
use function array_sum;
use function explode;
use function implode;
use function sprintf;
use function trim;

use const PHP_EOL;

/**
 * @psalm-api
 */
class Number extends Roll
{
    protected const int SUCCESS = 10;
    protected const int FAILURE = 6;

    protected int $modifier;
    protected int $result;
    /** @var array<int, int> */
    protected array $rolls = [];

    public function __construct(
        string $content,
        string $character,
        Channel $channel,
        public ?DiscordMessageReceived $event = null,
    ) {
        parent::__construct($content, $character, $channel);

        $args = explode(' ', trim($content));
        $this->modifier = (int)array_shift($args);
        $this->description = trim(implode(' ', $args));
        if ('' !== $this->description) {
            $this->description = sprintf(' for "%s"', $this->description);
        }

        $this->roll();
    }

    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL . $this->text;
    }

    public function forIrc(): string
    {
        return $this->title . PHP_EOL . $this->text;
    }

    public function forSlack(): SlackResponse
    {
        $color = TextAttachment::COLOR_DANGER;
        if (self::FAILURE < $this->result) {
            $color = TextAttachment::COLOR_SUCCESS;
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    protected function roll(): void
    {
        /** @psalm-suppress UndefinedClass */
        $this->rolls[] = DiceService::rollOne(6);
        /** @psalm-suppress UndefinedClass */
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
                self::SUCCESS <= $this->result => '%s got a full hit%s!',
                default => '%s got a partial hit%s!',
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
