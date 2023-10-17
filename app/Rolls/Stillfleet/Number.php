<?php

declare(strict_types=1);

namespace App\Rolls\Stillfleet;

use App\Events\DiscordMessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use App\Services\DiceService;

use function array_shift;
use function explode;
use function implode;
use function in_array;
use function is_numeric;
use function sprintf;
use function trim;

use const PHP_EOL;

class Number extends Roll
{
    protected const VALID_DICE = [
        2, 3, 4, 6, 8, 10, 12, 20, 30, 100,
    ];

    protected int $boost = 0;
    protected int $penalty = 0;
    protected int $die = 0;
    protected ?string $error = null;

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
        $this->die = (int)array_shift($args);
        if (
            !in_array(
                haystack: self::VALID_DICE,
                needle: $this->die,
                strict: true
            )
        ) {
            $this->error = sprintf(
                '%d is not a valid die size in Stillfleet',
                $this->die,
            );
        }

        if (isset($args[0]) && is_numeric($args[0])) {
            $number = (int)array_shift($args);
            if ($number > 0) {
                $this->boost = $number;
            } else {
                $this->penalty = abs($number);
            }
        }

        $this->description = implode(' ', $args);

        $this->roll();
    }

    public function forSlack(): SlackResponse
    {
        $attachment = new TextAttachment(
            'Title',
            'Text',
            TextAttachment::COLOR_SUCCESS
        );
        $response = new SlackResponse('', SlackResponse::HTTP_OK, [], $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $value = sprintf('**%s**', 'Title') . PHP_EOL
            . 'Text' . PHP_EOL;
        return $value;
    }

    protected function roll(): void
    {
        //$roll = DiceService::rollOne($this->die);
    }
}
