<?php

declare(strict_types=1);

namespace App\Rolls\Subversion;

use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;

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
                'title' => sprintf('About %s', config('app.name')),
                'text' => \sprintf(
                    '%s is a Slack/Discord bot that lets you roll dice '
                    . 'appropriate for various RPG systems. This channel is '
                    . 'playing Subversion, so your rolls will be some number '
                    . 'of six-sided dice, keeping the three highest.',
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
                    . 'highest' . \PHP_EOL
                    . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                    . 'optionally adding C to the result, optionally '
                    . 'describing that the roll is for "text"' . \PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
            return;
        }
        if (null === $this->chatUser) {
            $this->data[] = [
                'title' => 'Note for unregistered users:',
                'slackText' => \sprintf(
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
                'discordText' => \sprintf(
                    'Your Discord user has not been linked with a %s user. Go to '
                    . 'the settings page (<%s/settings>) and copy the command '
                    . 'listed there for this server. If the server isn\'t '
                    . 'listed, follow the instructions there to add it. '
                    . 'You\'ll need to know your server ID (`%s`) and your '
                    . 'user ID (`%s`).',
                    config('app.name'),
                    config('app.url'),
                    $this->channel->server_id,
                    $this->channel->user,
                ),
                'color' => TextAttachment::COLOR_DANGER,
            ];
        }
        $this->data[] = [
            'title' => 'Subversion commands (no character linked):',
            'text' => '· `9 [text]` - Roll 9 D6s with optional text (human '
                . 'perception, bargaining, etc) keeping the 3 hightest'
                . \PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . \PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
    }

    /**
     * Return the roll formatted for Slack.
     * @return SlackResponse
     */
    public function forSlack(): SlackResponse
    {
        $response = new SlackResponse(channel: $this->channel);
        foreach ($this->data as $element) {
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= \sprintf('**%s**', $element['title']) . \PHP_EOL
            . $element['text'] . \PHP_EOL;
        }
        return $value;
    }
}
