<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Models\Channel;
use App\Traits\FormulaConverter;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Contexts\PlainText;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Text;
use Override;

use function array_shift;
use function array_sum;
use function explode;
use function implode;
use function preg_match;
use function sprintf;
use function str_replace;

use const PHP_EOL;

/**
 * Class representing a generic XdY+C roll.
 */
class Generic extends Roll
{
    use FormulaConverter;

    public function __construct(
        string $content,
        string $username,
        Channel $channel
    ) {
        parent::__construct($content, $username, $channel);

        // First, pull the description part out, if it exists.
        $parts = explode(' ', $content);
        $expression = array_shift($parts);
        if ([] !== $parts) {
            $this->description = implode(' ', $parts);
        }

        $dynamicPart = $this->getDynamicPart($expression);
        [$dice, $pips] = $this->getDiceAndPips($dynamicPart);

        $rolls = $this->rollDice($dice, $pips);

        $diceSum = array_sum($rolls);

        // Swap out the XdY with the sum of the rolled dice to show our work.
        $partial = str_replace(
            $dynamicPart,
            sprintf('[%s]', implode('+', $rolls)),
            $expression
        );

        // Use the convertFormula trait from Shadowrun 5E to avoid needing
        // eval().
        $total = self::convertFormula(
            str_replace($dynamicPart, sprintf('%d', $diceSum), $content),
            'F', // unused
            1, // unused
        );

        $this->title = sprintf(
            '%s rolled %d%s',
            $username,
            $total,
            ('' !== $this->description) ? sprintf(' for "%s"', $this->description) : ''
        );
        $this->text = sprintf(
            'Rolling: %s = %s = %d',
            $expression,
            $partial,
            $total
        );
        if ($dice > 1) {
            $this->footer = 'Rolls: ' . implode(', ', $rolls);
        }
    }

    /**
     * Pull the dynamic part of the text out.
     *
     * For an expression like '10+9d6+27', would pull out and return '9d6'.
     */
    protected function getDynamicPart(string $string): string
    {
        $matches = [];
        preg_match('/(\d+)d(\d+)/', $string, $matches);
        return $matches[0];
    }

    /**
     * Convert a string like '1d6' into its two parts: 1 and 6.
     * @return array<int, int>
     */
    protected function getDiceAndPips(string $dynamicPart): array
    {
        $dicePart = explode('d', $dynamicPart);
        return [(int)$dicePart[0], (int)$dicePart[1]];
    }

    /**
     * Roll a certain number of dice with a certain number of pips.
     * @return array<int, int>
     */
    protected function rollDice(int $dice, int $pips): array
    {
        return DiceService::rollMany($dice, $pips);
    }

    #[Override]
    public function forDiscord(): string
    {
        $value = sprintf('**%s**', $this->title) . PHP_EOL
            . $this->text . PHP_EOL;
        if ('' !== $this->footer) {
            $value .= sprintf('_%s_', $this->footer);
        }
        return $value;
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->title . PHP_EOL . $this->text;
    }

    #[Override]
    public function forSlack(): Response
    {
        return (new Response())
            ->addBlock(new Header($this->title))
            ->addBlock(new Text($this->text))
            ->addBlock(new PlainText($this->footer))
            ->sendToChannel();
    }
}
