<?php

declare(strict_types=1);

namespace App\Rolls\Shadowrun5e;

use App\Events\DiscordMessageReceived;
use App\Exceptions\SlackException;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Discord\Builders\Components\ActionRow;
use Discord\Builders\Components\Button;
use Discord\Builders\MessageBuilder;
use Discord\Parts\Interactions\Interaction;

/**
 * Roll a Shadowrun number, representing a set of six-sided dice.
 */
class Number extends Roll
{
    protected const MAX_DICE = 100;
    protected const MIN_SUCCESS = 5;

    /**
     * Number of dice to roll.
     * @var int
     */
    protected int $dice = 0;

    protected ?string $error = null;

    /**
     * Number of failures the roll produced.
     * @var int
     */
    protected int $fails = 0;

    /**
     * Number of successes to keep.
     * @var ?int
     */
    protected ?int $limit = null;

    /**
     * Array of individual dice rolls.
     * @var array<int, int>
     */
    protected array $rolls = [];

    /**
     * Number of successes the roll produced.
     * @var int
     */
    protected int $successes = 0;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?DiscordMessageReceived $event = null
    ) {
        parent::__construct($content, $username, $channel);

        $args = \explode(' ', \trim($content));
        $this->dice = (int)\array_shift($args);
        if ($this->dice > self::MAX_DICE) {
            $this->error = 'You can\'t roll more than 100 dice!';
            return;
        }
        if (isset($args[0]) && \is_numeric($args[0])) {
            $this->limit = (int)\array_shift($args);
        }
        $this->description = \implode(' ', $args);

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
        $this->title = \sprintf(
            '%s rolled a critical glitch on %d dice!',
            $this->username,
            $this->dice
        );
        $this->text = \sprintf(
            'Rolled %d ones with no successes%s!',
            $this->fails,
            ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
        );
    }

    /**
     * Format the text and title for a normal roll.
     */
    protected function formatRoll(): void
    {
        $this->title = $this->formatTitle();
        if (null !== $this->limit && $this->limit < $this->successes) {
            $this->text = \sprintf(
                'Rolled %d successes%s, hit limit',
                $this->limit,
                ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
            );
            return;
        }

        $this->text = \sprintf(
            'Rolled %d successes%s',
            $this->successes,
            ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
        );
    }

    /**
     * Format the title part of the roll.
     * @return string
     */
    protected function formatTitle(): string
    {
        return \sprintf(
            '%s rolled %d %s%s%s',
            $this->username,
            $this->dice,
            1 === $this->dice ? 'die' : 'dice',
            null !== $this->limit ? \sprintf(' with a limit of %d', $this->limit) : '',
            $this->isGlitch() ? ', glitched' : ''
        );
    }

    /**
     * Roll the requested number of dice, checking for successes and failures.
     */
    protected function roll(): void
    {
        for ($i = 0; $i < $this->dice; $i++) {
            $this->rolls[] = $roll = random_int(1, 6);
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
            }
            if (1 === $roll) {
                $this->fails++;
            }
        }
        \rsort($this->rolls, \SORT_NUMERIC);
    }

    /**
     * Bold successes, strike out failures in the roll list.
     * @return array<int, string>
     */
    protected function prettifyRolls(): array
    {
        $rolls = $this->rolls;
        \array_walk($rolls, function (int &$value): void {
            if ($value >= self::MIN_SUCCESS) {
                $value = \sprintf('*%d*', $value);
            } elseif (1 == $value) {
                $value = \sprintf('~%d~', $value);
            }
        });
        // @phpstan-ignore-next-line
        return $rolls;
    }

    /**
     * Return whether the roll was a glitch.
     * @return bool
     */
    protected function isGlitch(): bool
    {
        if (0 === $this->fails) {
            // No matter how small the dice pool, no ones means it's not
            // a glitch.
            return false;
        }
        // If half of the dice were ones, it's a glitch.
        return $this->fails > \floor($this->dice / 2);
    }

    /**
     * Return whether the roll was a critical glitch.
     * @return bool
     */
    protected function isCriticalGlitch(): bool
    {
        return $this->isGlitch() && 0 === $this->successes;
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
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
        $footer = \implode(' ', $this->prettifyRolls());
        if (null !== $this->limit) {
            $footer .= \sprintf(', limit: %d', $this->limit);
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $attachment->addFooter($footer);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @psalm-suppress InvalidReturnType
     * @return string|MessageBuilder
     */
    public function forDiscord(): string | MessageBuilder
    {
        if (null !== $this->error) {
            return $this->error;
        }

        $footer = 'Rolls: ' . \implode(' ', $this->rolls);
        if (null !== $this->limit) {
            $footer .= \sprintf(', Limit: %d', $this->limit);
        }
        $content = \sprintf('**%s**', $this->title) . \PHP_EOL
            . $this->text . \PHP_EOL
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

        $button = Button::new(Button::STYLE_SUCCESS)
            ->setLabel('2nd chance')
            ->setListener([$this, 'secondChance'], $this->event->discord);
        $row = ActionRow::new()->addComponent($button);
        $message = new MessageBuilder();
        $message->setContent($content)->addComponent($row);
        /** @psalm-suppress InvalidReturnStatement */
        return $message;
    }

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
            if (5 === $roll || 6 === $roll) {
                continue;
            }
            $rerolled++;
            $this->rolls[$key] = $roll = random_int(1, 6);
            if (self::MIN_SUCCESS <= $roll) {
                $this->successes++;
            }
            if (1 === $roll) {
                $this->fails++;
            }
        }

        // Charge the character some edge.
        /** @var \App\Models\Shadowrun5e\Character */
        $character = $this->character;
        $character->edgeCurrent--;
        $character->save();

        $this->formatRoll();
        $footer = 'Rolls: ' . \implode(' ', $this->rolls);
        if (null !== $this->limit) {
            $footer .= \sprintf(', Limit: %d', $this->limit);
        }
        $content = \sprintf('**%s**', $this->title) . \PHP_EOL
            . $this->text . \PHP_EOL
            . \sprintf('Rerolled %d failures', $rerolled) . \PHP_EOL
            . $footer . \PHP_EOL
            . \sprintf('Remaining edge: %d', $character->edgeCurrent);

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
}
