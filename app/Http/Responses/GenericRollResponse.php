<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Events\RollEvent;
use App\Exceptions\SlackException;
use App\Models\Shadowrun5E\ForceTrait;
use App\Models\Slack\Channel;
use App\Models\Slack\TextAttachment;

class GenericRollResponse extends SlackResponse
{
    use ForceTrait;

    /**
     * Optional description the user added for the roll.
     * @var string
     */
    protected string $description = '';

    /**
     * Constructor.
     * @param string $content
     * @param int $status
     * @param array<string, string> $headers
     * @param ?Channel $channel
     * @throws SlackException
     */
    public function __construct(
        string $content = '',
        int $status = self::HTTP_OK,
        array $headers = [],
        ?Channel $channel = null
    ) {
        parent::__construct('', $status, $headers);
        if (is_null($channel)) {
            throw new SlackException(('Channel is required'));
        }

        // First, pull the description part out, if it exists.
        $parts = explode(' ', $content);
        $expression = array_shift($parts);
        if (0 !== count($parts)) {
            $this->description = implode(' ', $parts);
        }

        $dynamicPart = $this->getDynamicPart($expression);
        [$dice, $pips] = $this->getDiceAndPips($dynamicPart);

        $rolls = $this->rollDice($dice, $pips);

        $diceSum = array_sum($rolls);

        // Swap out the XdY with the sum of the rolled dice to show our work.
        $partial = str_replace(
            $dynamicPart,
            sprintf('[%d]', $diceSum),
            $expression
        );

        // Use the convertFormula trait from Shadowrun 5E to avoid needing
        // eval().
        $total = $this->convertFormula(
            str_replace($dynamicPart, sprintf('%d', $diceSum), $content),
            'F', // unused
            1 // unused
        );

        $title = sprintf(
            '%s rolled %d%s',
            $channel->username,
            $total,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
        $text = sprintf('Rolling: %s = %s = %d', $expression, $partial, $total);
        $attachment = (new TextAttachment(
            $title,
            $text,
            TextAttachment::COLOR_SUCCESS
        ))
            ->addFooter('Rolls: ' . implode(', ', $rolls));
        $this->addAttachment($attachment)->sendToChannel();
        RollEvent::dispatch($title, $text, $rolls, $channel);
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
        preg_match('/(\d+)d(\d+)/', $string, $matches);
        return $matches[0];
    }

    /**
     * Convert a string like '1d6' into its two parts: 1 and 6.
     * @param string $dynamicPart
     * @return array<int, int>
     */
    protected function getDiceAndPips(string $dynamicPart): array
    {
        $dicePart = explode('d', $dynamicPart);
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
}
