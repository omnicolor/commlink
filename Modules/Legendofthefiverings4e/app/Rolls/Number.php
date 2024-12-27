<?php

declare(strict_types=1);

namespace Modules\Legendofthefiverings4e\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function array_slice;
use function array_sum;
use function count;
use function explode;
use function implode;
use function is_numeric;
use function rsort;
use function sprintf;
use function trim;

use const PHP_EOL;
use const SORT_NUMERIC;

class Number extends Roll
{
    protected int $dice;
    protected int $keep;
    protected ?string $error = null;
    /** @var array<int, int> */
    protected array $rolls = [];

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', trim($content));
        if (
            2 > count($args)
            || !is_numeric($args[0])
            || !is_numeric($args[1])
        ) {
            $this->error = 'LotFR rolls require two numbers: how many dice to '
                . 'roll and how many to keep.';
            return;
        }
        $this->dice = (int)array_shift($args);
        $this->keep = (int)array_shift($args);

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

        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            TextAttachment::COLOR_SUCCESS,
        );
        $attachment->addFooter(implode(' ', $this->rolls));
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Roll the required number of dice, exploding the 10s. Then keep the
     * requested number of highest rolls.
     */
    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            $result = 0;
            do {
                $roll = DiceService::rollOne(10);
                $result += $roll;
            } while (10 === $roll);
            $this->rolls[] = $result;
        }
        rsort($this->rolls, SORT_NUMERIC);

        $result = array_sum(array_slice($this->rolls, 0, $this->keep));
        $this->title = sprintf(
            '%s rolled %d%s',
            $this->username,
            $result,
            $this->description,
        );
        $this->text = sprintf('Rolled %d, kept %d', $this->dice, $this->keep);
    }
}
