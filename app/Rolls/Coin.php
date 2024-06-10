<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use Facades\App\Services\DiceService;

use function sprintf;

use const PHP_EOL;

/**
 * Class representing a coin flip.
 */
class Coin extends Roll
{
    /**
     * @psalm-suppress UndefinedClass
     */
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

    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL;
    }

    public function forIrc(): string
    {
        return $this->title;
    }

    public function forSlack(): SlackResponse
    {
        $attachment = new TextAttachment($this->title, $this->text);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }
}
