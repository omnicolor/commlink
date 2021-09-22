<?php

declare(strict_types=1);

namespace App\Rolls\Cyberpunkred;

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

    /**
     * Constructor.
     * @param string $content
     * @param string $character
     * @param Channel $channel
     */
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
                    . 'playing Cyberpunk Red, so most of your rolls will be '
                    . '1d10 + skill + attribute + modifier. Rolling a 1 '
                    . 'requires an additional roll that is subtracted from '
                    . 'your result, while rolling a 10 adds an additional '
                    . 'roll. Commlink handles that for you with a simple '
                    . '`/roll 6 5 -2` (for example) that will roll a ten-sided '
                    . 'die, add six for your attribute and 5 for your still, '
                    . 'then subtract two for your modifiers to give you a '
                    . 'final result of nine plus whatever the dice say.',
                    config('app.name'),
                ),
                'color' => TextAttachment::COLOR_INFO,
            ],
        ];
        if (null !== $this->character) {
            $this->data[] = [
                'title' => sprintf(
                    'Cyberpunk Red commands (as %s):',
                    (string)$this->character
                ),
                'text' => sprintf(
                    '· `9 [text]` - Roll 1d10 adding 9 to the result, with '
                        . 'optional text (human perception, bargaining, etc)'
                        . \PHP_EOL
                        . '· `6 5 -2 [text]` - Roll 1d10, adding 6 '
                        . '(attribute), 5 (skill), and -2 (modifier) to the '
                        . 'result' . \PHP_EOL
                        . '· `init [-2]` - Roll initiative as %s (reflexes %d) '
                        . 'with an optional modifier of -2' . \PHP_EOL
                        . '· `XdY[+C] [text]` - Roll X dice with Y sides, '
                        . 'optionally adding C to the result, optionally '
                        . 'describing that the roll is for "text"' . \PHP_EOL,
                    (string)$this->character,
                    // @phpstan-ignore-next-line
                    $this->character->reflexes,
                ),
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
            'title' => 'Cyberpunk Red commands (no character linked):',
            'text' => '· `9 [text]` - Roll 1d10 adding 9 to the result, with '
                . 'optional text (human perception, bargaining, etc)' . \PHP_EOL
                . '· `6 5 -2 [text]` - Roll 1d10, adding 6 (attribute), 5 '
                . '(skill), and -2 (modifier) to the result' . \PHP_EOL
                . '· `init 8 [-2]` - Roll initiative for a character with '
                . 'reflexes of 8, optionally with a modifier of -2' . \PHP_EOL
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

    /**
     * Return the roll formatted for Discord.
     * @return string
     */
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= \sprintf('**%s**', $element['title']) . \PHP_EOL
                . ($element['discordText'] ?? $element['text'])
                . \PHP_EOL . \PHP_EOL;
        }
        return $value;
    }
}
