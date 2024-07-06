<?php

declare(strict_types=1);

namespace App\Rolls\Stillfleet;

use App\Events\DiscordMessageReceived;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function explode;
use function implode;
use function in_array;
use function is_numeric;
use function sprintf;
use function trim;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Number extends Roll
{
    protected const VALID_DICE = [
        2, 3, 4, 6, 8, 10, 12, 20, 30, 100,
    ];

    protected int $boost = 0;
    protected int $penalty = 0;
    protected int $die = 0;
    protected ?string $error = null;
    protected int $roll;
    protected int $result;

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

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $value = sprintf('**%s rolled a %d**', $this->username, $this->result)
            . PHP_EOL
            . (string)$this->roll;
        if (0 !== $this->boost) {
            $value .= ' + ' . (string)$this->boost;
        } elseif (0 !== $this->penalty) {
            $value .= ' - ' . (string)$this->penalty;
        }
        return $value;
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $value = sprintf('%s rolled a %d', $this->username, $this->result)
            . PHP_EOL
            . (string)$this->roll;
        if (0 !== $this->boost) {
            $value .= ' + ' . (string)$this->boost;
        } elseif (0 !== $this->penalty) {
            $value .= ' - ' . (string)$this->penalty;
        }
        return $value;
    }

    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        $value = (string)$this->roll;
        if (0 !== $this->boost) {
            $value .= ' + ' . (string)$this->boost;
        } elseif (0 !== $this->penalty) {
            $value .= ' - ' . (string)$this->penalty;
        }

        $attachment = new TextAttachment(
            sprintf('%s rolled a %d', $this->username, $this->result),
            $value,
        );
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    protected function roll(): void
    {
        $this->roll = DiceService::rollOne($this->die);
        $this->result = $this->roll + $this->boost - $this->penalty;
    }
}
