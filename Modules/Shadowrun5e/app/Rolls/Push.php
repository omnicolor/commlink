<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Rolls;

use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function array_shift;
use function explode;
use function floor;
use function implode;
use function is_numeric;
use function rsort;
use function sprintf;
use function trim;

use const PHP_EOL;
use const SORT_NUMERIC;

/**
 * Roll a Shadowrun 5e push the limit test.
 *
 * Add your Edge rating to your test, either before or after the roll. This can
 * allow you to take tests that might otherwise have a dice pool of zero or less
 * thanks to various modifiers in play. Using Edge in this way makes the Rule of
 * Six come into play: for every 6 you roll, count it as a hit and then re-roll
 * that die, adding any additional hits from the re-roll to your total. If you
 * decide to use this function after your initial roll, only your Edge dice use
 * the Rule of Six. This use of Edge also allows you to ignore any limit on your
 * test.
 * @psalm-suppress UnusedClass
 */
class Push extends Number
{
    protected const MAX_DICE = 100;
    protected const MIN_SUCCESS = 5;
    protected const EXPLODING_SIX = 6;
    protected const FAILURE = 1;

    /**
     * User's description of what they're rolling for.
     */
    protected string $description;

    /**
     * Number of dice to roll.
     */
    protected int $dice = 0;

    /**
     * Character's current edge.
     */
    protected int $edge;

    /**
     * Error message to return to the user.
     */
    protected ?string $error = null;

    /**
     * Number of sixes that exploded.
     */
    protected int $exploded = 0;

    /**
     * Number of failures the roll produced.
     */
    protected int $fails = 0;

    /**
     * Number of successes to keep.
     */
    protected ?int $limit = null;

    /**
     * Array of individual dice rolls.
     * @var array<int, int>
     */
    protected array $rolls = [];

    /**
     * Number of successes the roll produced.
     */
    protected int $successes = 0;

    // @phpstan-ignore-next-line
    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        Roll::__construct($content, $username, $channel);

        if (null === $this->character) {
            $this->error = 'You must have a character linked to push the limit';
            return;
        }
        $args = explode(' ', trim($content));

        // Remove the name of the command.
        array_shift($args);

        if (!is_numeric($args[0])) {
            $this->error = 'Pushing the limit requires the number of dice to '
                . 'roll (not including your edge)';
            return;
        }

        $this->dice = (int)array_shift($args);
        if (self::MAX_DICE < $this->dice) {
            $this->error = 'You can\'t roll more than 100 dice';
            return;
        }

        // @phpstan-ignore-next-line
        if (null === $this->character->edgeCurrent) {
            $this->character->edgeCurrent = $this->character->edge ?? 0;
        }
        if (0 === $this->character->edgeCurrent) {
            $this->error = 'It looks like you\'re out of edge!';
            return;
        }
        // @phpstan-ignore-next-line
        $this->edge = $this->character->edge;
        if (isset($args[0]) && is_numeric($args[0])) {
            $this->limit = (int)array_shift($args);
        }
        $this->description = implode(' ', $args);

        $this->roll();

        if ($this->isCriticalGlitch()) {
            $this->formatCriticalGlitch();
        } else {
            $this->formatRoll();
        }

        // @phpstan-ignore-next-line
        $this->character->edgeCurrent--;
        // @phpstan-ignore-next-line
        $this->character->save();
    }

    /**
     * Format the text and title for a critical glitch.
     */
    protected function formatCriticalGlitch(): void
    {
        $this->title = sprintf(
            '%s rolled a critical glitch on %d (%d requested + %d edge) dice!',
            $this->username,
            $this->dice + $this->edge,
            $this->dice,
            $this->edge,
        );
        $this->text = sprintf(
            'Rolled %d ones with no successes%s!',
            $this->fails,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
    }

    /**
     * Format the text and title for a normal roll.
     */
    protected function formatRoll(): void
    {
        $this->title = $this->formatTitle();
        if (null !== $this->limit && $this->limit < $this->successes) {
            $this->text = sprintf(
                'Rolled %d successes, blew past limit',
                $this->successes,
            );
            return;
        }

        $this->text = sprintf(
            'Rolled %d successes',
            $this->successes,
        );
    }

    /**
     * Format the title part of the roll.
     */
    protected function formatTitle(): string
    {
        return sprintf(
            '%s rolled %d (%d requested + %d edge) dice%s%s%s',
            $this->username,
            $this->dice + $this->edge,
            $this->dice,
            $this->edge,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : '',
            null !== $this->limit ? sprintf(' with a limit of %d', $this->limit) : '',
            $this->isGlitch() ? ', glitched' : ''
        );
    }

    /**
     * Roll the requested number of dice, checking for successes and failures.
     * @psalm-suppress UndefinedClass
     */
    protected function roll(): void
    {
        // @phpstan-ignore-next-line
        for ($i = 0; $i < $this->dice + $this->character->edge; $i++) {
            $this->rolls[] = $roll = DiceService::rollOne(6);
            if (self::EXPLODING_SIX === $roll) {
                // Explode the six.
                $i--;
                $this->exploded++;
            }
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
                continue;
            }
            if (self::FAILURE === $roll) {
                $this->fails++;
            }
        }
        rsort($this->rolls, SORT_NUMERIC);
    }

    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        $footer = 'Rolls: ' . implode(' ', $this->rolls)
            . ' (' . $this->exploded . ' exploded)';
        if (null !== $this->limit) {
            $footer .= sprintf(', Limit: %d', $this->limit);
        }
        return sprintf('**%s**', $this->title) . PHP_EOL
            . $this->text . PHP_EOL
            . $footer;
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }
        $footer = 'Rolls: ' . implode(' ', $this->rolls)
            . ' (' . $this->exploded . ' exploded)';
        if (null !== $this->limit) {
            $footer .= sprintf(', Limit: %d', $this->limit);
        }
        return $this->title . PHP_EOL
            . $this->text . PHP_EOL
            . $footer;
    }

    /**
     * @throws SlackException
     */
    public function forSlack(): SlackResponse
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }
        $color = TextAttachment::COLOR_SUCCESS;
        if ($this->isCriticalGlitch() || $this->isGlitch()) {
            $color = TextAttachment::COLOR_DANGER;
        }
        $footer = implode(' ', $this->prettifyRolls())
            . ' (' . $this->exploded . ' exploded)';
        if (null !== $this->limit) {
            $footer .= sprintf(', limit: %d', $this->limit);
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $attachment->addFooter($footer);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return whether the roll was a glitch.
     */
    protected function isGlitch(): bool
    {
        if (0 === $this->fails) {
            // No matter how small the dice pool, no ones means it's not
            // a glitch.
            return false;
        }
        // If half of the dice were ones, it's a glitch.
        return $this->fails > floor(($this->dice + $this->edge) / 2);
    }

    /**
     * Return whether the roll was a critical glitch.
     */
    protected function isCriticalGlitch(): bool
    {
        return $this->isGlitch() && 0 === $this->successes;
    }
}
