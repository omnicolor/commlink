<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Rolls;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Text;
use Override;

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

    #[Override]
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

    #[Override]
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

    #[Override]
    public function forSlack(): Response
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

        return (new Response())
            ->addBlock(new Header(sprintf('%s rolled a %d', $this->username, $this->result)))
            ->addBlock(new Text($value))
            ->sendToChannel();
    }

    protected function roll(): void
    {
        $this->roll = DiceService::rollOne($this->die);
        $this->result = $this->roll + $this->boost - $this->penalty;
    }
}
