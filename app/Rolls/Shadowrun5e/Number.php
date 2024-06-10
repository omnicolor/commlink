<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Events\DiscordMessageReceived;
use App\Events\MessageReceived;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Shadowrun5e\Character;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;
use Facades\App\Services\DiceService;
use MathPHP\Probability\Combinatorics;

use function array_shift;
use function array_walk;
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
 * Roll a Shadowrun number, representing a set of six-sided dice.
 */
class Number extends Roll
{
    protected const MAX_DICE = 100;
    protected const MIN_SUCCESS = 5;
    protected const FAILURE = 1;

    protected int $dice = 0;
    protected ?string $error = null;
    protected int $fails = 0;
    protected ?int $limit = null;
    /** @var array<int, int> */
    protected array $rolls = [];
    protected int $successes = 0;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?MessageReceived $event = null,
    ) {
        parent::__construct($content, $username, $channel);

        $args = explode(' ', trim($content));
        $this->dice = (int)array_shift($args);
        if ($this->dice > self::MAX_DICE) {
            $this->error = 'You can\'t roll more than 100 dice!';
            return;
        }
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
    }

    /**
     * Format the text and title for a critical glitch.
     */
    protected function formatCriticalGlitch(): void
    {
        $this->title = sprintf(
            '%s rolled a critical glitch on %d dice!',
            $this->username,
            $this->dice
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
                'Rolled %d successes%s, hit limit',
                $this->limit,
                ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
            );
            return;
        }

        $this->text = sprintf(
            'Rolled %d successes%s',
            $this->successes,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
    }

    protected function formatTitle(): string
    {
        return sprintf(
            '%s rolled %d %s%s%s',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice',
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
        for ($i = 0; $i < $this->dice; $i++) {
            $this->rolls[] = $roll = DiceService::rollOne(6);
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
            }
            if (self::FAILURE === $roll) {
                $this->fails++;
            }
        }
        rsort($this->rolls, SORT_NUMERIC);
    }

    /**
     * Bold successes, strike out failures in the roll list.
     * @return array<int, string>
     */
    protected function prettifyRolls(): array
    {
        $rolls = $this->rolls;
        array_walk($rolls, function (int &$value): void {
            if ($value >= self::MIN_SUCCESS) {
                $value = sprintf('*%d*', $value);
            } elseif (self::FAILURE == $value) {
                $value = sprintf('~%d~', $value);
            }
        });
        // @phpstan-ignore-next-line
        return $rolls;
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
        return $this->fails > floor($this->dice / 2);
    }

    /**
     * Return whether the roll was a critical glitch.
     * @return bool
     */
    protected function isCriticalGlitch(): bool
    {
        return $this->isGlitch() && 0 === $this->successes;
    }

    public function forDiscord(): string | MessageBuilder
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $footer = 'Rolls: ' . implode(' ', $this->rolls);
        if (null !== $this->limit) {
            $footer .= sprintf(', Limit: %d', $this->limit);
        }
        $probability = $this->getProbability($this->dice, $this->successes);
        if (1.0 !== $probability) {
            $footer .= sprintf(', Probability: %01.4f%%', $this->getProbability($this->dice, $this->successes) * 100);
        }
        $content = sprintf('**%s**', $this->title) . PHP_EOL
            . $this->text . PHP_EOL
            . $footer;

        if (!isset($this->event) || null === $this->character) {
            return $content;
        }

        // @phpstan-ignore-next-line
        if (null === $this->character->edgeCurrent) {
            $this->character->edgeCurrent = $this->character->edge ?? 0;
        }

        // If the character is out of edge or glitched, second chance can't be
        // used.
        if (
            0 === $this->character->edgeCurrent
            || $this->isGlitch()
            || $this->isCriticalGlitch()
        ) {
            return $content;
        }

        /** @var DiscordMessageReceived */
        $event = $this->event;
        $button = Button::new(Button::STYLE_SUCCESS)
            ->setLabel('2nd chance')
            ->setListener([$this, 'secondChance'], $event->discord);
        $row = ActionRow::new()->addComponent($button);
        $message = new MessageBuilder();
        $message->setContent($content)->addComponent($row);
        return $message;
    }

    public function forIrc(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $footer = 'Rolls: ' . implode(' ', $this->rolls);
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
        $footer = implode(' ', $this->prettifyRolls());

        if (null !== $this->limit) {
            $footer .= sprintf(', Limit: %d', $this->limit);
        }

        $probability = $this->getProbability($this->dice, $this->successes);
        if (1.0 !== $probability) {
            $footer .= sprintf(
                ', Probability: %01.4f%%',
                $this->getProbability($this->dice, $this->successes) * 100
            );
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $attachment->addFooter($footer);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    public function secondChance(Interaction $interaction): void
    {
        // Only the user that originally rolled can second chance.
        // @phpstan-ignore-next-line
        if ($interaction->message->referenced_message->author->id !== $interaction->user->id) {
            return;
        }

        // Reroll the failures only.
        $rerolled = 0;
        $this->fails = 0;
        foreach ($this->rolls as $key => $roll) {
            if (self::MIN_SUCCESS <= $roll) {
                continue;
            }
            $rerolled++;
            $this->rolls[$key] = $roll = DiceService::rollOne(6);
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
            }
            if (self::FAILURE === $roll) {
                $this->fails++;
            }
        }

        // Charge the character some edge.
        /** @var Character */
        $character = $this->character;
        $character->edgeCurrent--;
        $character->save();

        $this->formatRoll();
        $footer = 'Rolls: ' . implode(' ', $this->rolls);
        if (null !== $this->limit) {
            $footer .= sprintf(', Limit: %d', $this->limit);
        }
        $content = sprintf('**%s**', $this->title) . PHP_EOL
            . $this->text . PHP_EOL
            . sprintf('Rerolled %d failures', $rerolled) . PHP_EOL
            . $footer . PHP_EOL
            . sprintf('Remaining edge: %d', $character->edgeCurrent);

        $button = Button::new(Button::STYLE_SECONDARY)
            ->setLabel('2nd chance')
            ->setDisabled(true);
        $row = ActionRow::new()->addComponent($button);
        $message = MessageBuilder::new()->setContent($content)
            ->addComponent($row);
        /** @psalm-suppress TooManyTemplateParams */
        // @phpstan-ignore-next-line
        $interaction->message->edit($message);
    }

    protected function getProbability(int $dice, int $successes): float
    {
        // Impossible to get more successes than there are dice.
        if ($dice < $successes) {
            return 0.0;
        }

        // Return the chance to get exactly the number of successes requested,
        // then add the probability for exactly one more success than that, etc.
        return Combinatorics::combinations($dice, $successes)
            * pow(2 / 3, $dice - $successes)
            * pow(1 / 3, $successes)
            + $this->getProbability($dice, $successes + 1);
    }
}
