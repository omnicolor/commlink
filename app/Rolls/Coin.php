<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Models\Channel;
use Facades\App\Services\DiceService;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Text;
use Override;

use function sprintf;

use const PHP_EOL;

/**
 * Class representing a coin flip.
 */
class Coin extends Roll
{
    public function __construct(
        string $content,
        string $username,
        Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $flip = DiceService::rollOne(2);
        $this->title = sprintf(
            '%s flipped a coin: %s',
            $username,
            1 === $flip ? 'Heads' : 'Tails'
        );
        $this->text = '';
    }

    #[Override]
    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL;
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->title;
    }

    #[Override]
    public function forSlack(): Response
    {
        return (new Response())
            ->addBlock(new Text($this->title))
            ->sendToChannel();
    }
}
