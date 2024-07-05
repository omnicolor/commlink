<?php

declare(strict_types=1);

namespace Modules\Startrekadventures\Rolls;

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

    /**
     * Constructor.
     */
    public function __construct(
        string $content,
        string $username,
        protected Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $this->data[] = [
            'title' => 'Commlink - Star Trek Adventures',
            'text' => 'Commlink is a bot that lets you roll '
                . 'Star Trek Adventures dice.' . PHP_EOL
                . '· `unfocused <att> <disc> <diff> [extra] [text]` - Attempt '
                . 'a task without an applicable focus: Roll two d20s plus any '
                . '[extra] dice with an attribute of <att>, a discipline of '
                . '<disc>, and a difficulty of <diff>, with optional text '
                . '[text].' . PHP_EOL
                . '· `focused <att> <disc> <diff> [extra] [text]` - Attempt '
                . 'a task with an applicable focus. Parameters are the same as '
                . 'an unfocused task.' . PHP_EOL
                . '· `challenge <dice> [text]` - Roll <dice> challenge dice, '
                . 'optionally adding that the roll is for "text".' . PHP_EOL
                . '· `Xd6 [text]` - Roll X sixed-sided dice, optionally '
                . 'describing that the roll is for "text".' . PHP_EOL
                . '· `Xd20 [text]` - Roll X twenty-sided dice, optionally '
                . 'describing that the roll is for "text".' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];

        if (null === $this->chatUser) {
            $this->data[] = [
                'color' => TextAttachment::COLOR_DANGER,
                'discordText' => sprintf(
                    'Your Discord user has not been linked with a %s user. Go '
                        . 'to the settings page (%s/settings) and copy the '
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
                    'Your IRC user has not been linked with a %s user. Go '
                        . 'to the settings page (%s/settings) and copy the '
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
        } elseif ($this->isGm()) {
            $this->data[] = [
                'title' => 'Gamemaster commands',
                'text' => '· None yet',
                'color' => TextAttachment::COLOR_INFO,
            ];
        } elseif (null === $this->character) {
            $this->data[] = [
                'title' => 'Player commands',
                'text' => '· `link <characterId>` - Link your Commlink '
                    . 'character to the channel.' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } else {
            $this->data[] = [
                'title' => 'Player',
                'text' => sprintf('Linked to %s', $this->character),
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
    }

    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            if (isset($element['discordText'])) {
                $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                    . $element['discordText'] . PHP_EOL;
                continue;
            }
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            if (isset($element['ircText'])) {
                $value .= $element['title'] . PHP_EOL
                    . $element['ircText'] . PHP_EOL;
                continue;
            }
            $value .= $element['title'] . PHP_EOL . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    public function forSlack(): SlackResponse
    {
        $response = new SlackResponse(
            '',
            SlackResponse::HTTP_OK,
            [],
            $this->channel
        );
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
