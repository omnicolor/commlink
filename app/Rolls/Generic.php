<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Shadowrun5E\ForceTrait;
use App\Models\Slack\TextAttachment;

/**
 * Class representing a generic XdY+C roll.
 */
class Generic extends Roll
{
    use ForceTrait;

    /**
     * Constructor.
     * @param string $content
     * @param string $character
     */
    public function __construct(string $content, string $character)
    {
        // First, pull the description part out, if it exists.
        $parts = \explode(' ', $content);
        $expression = \array_shift($parts);
        if (0 !== \count($parts)) {
            $this->description = \implode(' ', $parts);
        }

        $dynamicPart = $this->getDynamicPart($expression);
        [$dice, $pips] = $this->getDiceAndPips($dynamicPart);

        $rolls = $this->rollDice($dice, $pips);

        $diceSum = \array_sum($rolls);

        // Swap out the XdY with the sum of the rolled dice to show our work.
        $partial = \str_replace(
            $dynamicPart,
            \sprintf('[%d]', $diceSum),
            $expression
        );

        // Use the convertFormula trait from Shadowrun 5E to avoid needing
        // eval().
        $total = $this->convertFormula(
            \str_replace($dynamicPart, \sprintf('%d', $diceSum), $content),
            'F', // unused
            1 // unused
        );

        $this->title = \sprintf(
            '%s rolled %d%s',
            $character,
            $total,
            ('' !== $this->description) ? \sprintf(' for "%s"', $this->description) : ''
        );
        $this->text = \sprintf('Rolling: %s = %s = %d', $expression, $partial, $total);
        if ($dice > 1) {
            $this->footer = 'Rolls: ' . \implode(', ', $rolls);
        }
    }

    /**
     * Pull the dynamic part of the text out.
     *
     * For an expression like '10+9d6+27', would pull out and return '9d6'.
     * @param string $string
     * @return string
     */
    protected function getDynamicPart(string $string): string
    {
        $matches = [];
        \preg_match('/(\d+)d(\d+)/', $string, $matches);
        return $matches[0];
    }

    /**
     * Convert a string like '1d6' into its two parts: 1 and 6.
     * @param string $dynamicPart
     * @return array<int, int>
     */
    protected function getDiceAndPips(string $dynamicPart): array
    {
        $dicePart = \explode('d', $dynamicPart);
        return [(int)$dicePart[0], (int)$dicePart[1]];
    }

    /**
     * Roll a certain number of dice with a certain number of pips.
     * @param int $dice
     * @param int $pips
     * @return array<int, int>
     */
    protected function rollDice(int $dice, int $pips): array
    {
        $rolls = [];
        for ($i = 0; $i < $dice; $i++) {
            $rolls[] = random_int(1, $pips);
        }
        return $rolls;
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(Channel $channel): SlackResponse
    {
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            TextAttachment::COLOR_SUCCESS
        );
        $attachment->addFooter($this->footer);
        $response = new SlackResponse('', SlackResponse::HTTP_OK, [], $channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        $value = \sprintf('**%s**', $this->title) . \PHP_EOL
            . $this->text . \PHP_EOL;
        if ('' !== $this->footer) {
            $value .= \sprintf('_%s_', $this->footer);
        }
        return $value;
    }
}
