<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Modules\Cyberpunkred\Models\Character;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

use function config;
use function sprintf;

use const PHP_EOL;

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
                'text' => sprintf(
                    '%s is a Slack/Discord bot that lets you roll dice '
                    . 'appropriate for various RPG systems. This channel is '
                    . 'playing Cyberpunk Red, so most of your rolls will be '
                    . '1d10 + skill + attribute + modifier. Rolling a 1 '
                    . 'requires an additional roll that is subtracted from '
                    . 'your result, while rolling a 10 adds an additional '
                    . 'roll. Commlink handles that for you with a simple '
                    . '`/roll 6 5 -2` (for example) that will roll a ten-sided '
                    . 'die, add six for your attribute and 5 for your skill, '
                    . 'then subtract two for your modifiers to give you a '
                    . 'final result of nine plus whatever the dice say.',
                    config('app.name'),
                ),
                'color' => TextAttachment::COLOR_INFO,
            ],
        ];
        if (null !== $this->character) {
            /** @var Character */
            $character = $this->character;
            $this->data[] = [
                'title' => sprintf(
                    'Cyberpunk Red commands (as %s):',
                    (string)$character
                ),
                'text' => sprintf(
                    '· `9 [text]` - Roll 1d10 adding 9 to the result, with '
                        . 'optional text (human perception, bargaining, etc)'
                        . PHP_EOL
                        . '· `6 5 -2 [text]` - Roll 1d10, adding 6 '
                        . '(attribute), 5 (skill), and -2 (modifier) to the '
                        . 'result' . PHP_EOL
                        . '· `init [-2]` - Roll initiative as %s (reflexes %d) '
                        . 'with an optional modifier of -2' . PHP_EOL
                        . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                        . 'optionally adding C to the result, optionally '
                        . 'describing that the roll is for "text"' . PHP_EOL,
                    (string)$character,
                    $character->reflexes,
                ),
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
                    'Your IRC user has not been linked with a %s user. Go '
                        . 'to the settings page (<%s/settings>) and copy the '
                        . 'command listed there for this server. If the server '
                        . 'isn\'t listed, follow the instructions there to add '
                        . 'it. You\'ll need to know your server ID (%s) and '
                        . 'your user ID (%s).',
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
            'title' => 'Cyberpunk Red commands (no character linked):',
            'text' => '· `9 [text]` - Roll 1d10 adding 9 to the result, with '
                . 'optional text (human perception, bargaining, etc)' . PHP_EOL
                . '· `6 5 -2 [text]` - Roll 1d10, adding 6 (attribute), 5 '
                . '(skill), and -2 (modifier) to the result' . PHP_EOL
                . '· `init 8 [-2]` - Roll initiative for a character with '
                . 'reflexes of 8, optionally with a modifier of -2' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];
    }

    #[Override]
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
                . ($element['discordText'] ?? $element['text'])
                . PHP_EOL . PHP_EOL;
        }
        return $value;
    }

    #[Override]
    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= $element['title'] . PHP_EOL
                . ($element['ircText'] ?? $element['text'])
                . PHP_EOL . PHP_EOL;
        }
        return $value;
    }

    #[Override]
    public function forSlack(): Response
    {
        $response = new Response();
        foreach ($this->data as $element) {
            // @phpstan-ignore method.deprecated
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['slackText'] ?? $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }
}
