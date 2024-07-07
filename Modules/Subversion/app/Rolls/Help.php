<?php

declare(strict_types=1);

namespace Modules\Subversion\Rolls;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

use function sprintf;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Help extends Roll
{
    /**
     * @var array<int, array<string, string>>
     */
    protected array $data = [];

    public function __construct(
        string $content,
        string $character,
        Channel $channel
    ) {
        parent::__construct($content, $character, $channel);
        $this->data = [
            [
                'title' => sprintf('%s - Subversion', config('app.name')),
                'text' => sprintf(
                    '%s is a Slack/Discord bot that lets you roll dice '
                    . 'appropriate for various RPG systems. This channel is '
                    . 'playing Subversion, so your rolls will usually be some '
                    . 'number of six-sided dice, keeping the three highest.',
                    config('app.name'),
                ),
                'color' => TextAttachment::COLOR_INFO,
            ],
        ];
        if (null !== $this->character) {
            $this->data[] = [
                'title' => sprintf(
                    'Subversion commands (as %s):',
                    (string)$this->character
                ),
                'text' => '· `9 [text]` - Roll 9 D6s, with optional text '
                    . '(human perception, bargaining, etc), keeping the three '
                    . 'highest. Rolling fewer than 3 automatically applies a '
                    . 'dulled condition to the roll.' . PHP_EOL
                    . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                    . 'optionally adding C to the result, optionally '
                    . 'describing that the roll is for "text".' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
            return;
        }
        if (null === $this->chatUser) {
            $this->data[] = [
                'color' => TextAttachment::COLOR_DANGER,
                'discordText' => sprintf(
                    'Your Discord user has not been linked with a %s user. Go '
                        . 'to the settings page (<%s/settings>) and copy the '
                        . 'command listed there for this server. If the server '
                        . 'isn\'t listed, follow the instructions there to add '
                        . 'it. You\'ll need to know your server ID (`%s`) and '
                        . 'your user ID (`%s`).',
                    config('app.name'),
                    config('app.url'),
                    $this->channel->server_id,
                    $this->channel->user,
                ),
                'ircText' => sprintf(
                    'Your IRC user has not been linked with a %s user. Go to '
                        . 'the settings page (<%s/settings>) and copy the '
                        . 'command listed there for this server. If the server '
                        . 'isn\'t listed, follow the instructions there to add '
                        . 'it. You\'ll need to know your server ID (`%s`) and '
                        . 'your user ID (`%s`).',
                    config('app.name'),
                    config('app.url'),
                    $this->channel->server_id,
                    $this->channel->user,
                ),
                'slackText' => sprintf(
                    'Your Slack user has not been linked with a %s user. '
                        . 'Go to the <%s/settings|settings page> and copy the '
                        . 'command listed there for this server. If the server '
                        . 'isn\'t listed, follow the instructions there to add '
                        . 'it. You\'ll need to know your server ID (`%s`) and '
                        . 'your user ID (`%s`).',
                    config('app.name'),
                    config('app.url'),
                    $this->channel->server_id,
                    $this->channel->user
                ),
                'title' => 'Note for unregistered users:',
            ];
        }
        $this->data[] = [
            'title' => 'Subversion commands (no character linked):',
            'text' => '· `9 [text]` - Roll 9 D6s, with optional text '
                . '(human perception, bargaining, etc), keeping the three '
                . 'highest. Rolling fewer than 3 automatically applies a '
                . 'dulled condition to the roll.' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                . 'optionally adding C to the result, optionally '
                . 'describing that the roll is for "text".' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
    }

    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                . ($element['discordText'] ?? $element['text']) . PHP_EOL;
        }
        return $value;
    }

    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= $element['title'] . PHP_EOL
                . ($element['discordText'] ?? $element['text']) . PHP_EOL;
        }
        return $value;
    }

    public function forSlack(): SlackResponse
    {
        $response = new SlackResponse(channel: $this->channel);
        foreach ($this->data as $element) {
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['slackText'] ?? $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }
}
