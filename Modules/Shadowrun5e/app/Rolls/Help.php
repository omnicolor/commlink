<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Rolls;

use App\Models\Channel;
use App\Rolls\Roll;
use Modules\Shadowrun5e\Models\Character;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use Override;

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
        string $username,
        protected Channel $channel,
    ) {
        parent::__construct($content, $username, $channel);

        $this->data[] = [
            'title' => 'Commlink - Shadowrun 5th Edition',
            'text' => 'Commlink is a Slack/Discord bot that lets you roll '
                . 'Shadowrun 5E dice.' . PHP_EOL
                . '· `6 [text]` - Roll 6 dice, with optional text (automatics, '
                . 'perception, etc)' . PHP_EOL
                . '· `12 6 [text]` - Roll 12 dice with a limit of 6' . PHP_EOL
                . '· `XdY[+C] [text]` - Roll X dice with Y sides, optionally '
                . 'adding C to the result, optionally describing that the roll '
                . 'is for "text"' . PHP_EOL,
            'color' => TextAttachment::COLOR_INFO,
        ];

        if ($this->isGm()) {
            $this->data[] = [
                'title' => 'Gamemaster commands',
                'text' => '· `init start` - Start a new initiative tracker, '
                    . 'removing any existing rolls' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        } elseif (null !== $this->character) {
            /** @var Character */
            $character = $this->character;

            $this->data[] = [
                'title' => 'Player',
                'text' => 'You\'re playing ' . $this->character
                    . ' in this channel' . PHP_EOL
                    . sprintf(
                        '· `composure` - Make a composure roll (%d)' . PHP_EOL
                        . '· `judge` - Make a judge intentions check (%d)'
                        . PHP_EOL
                        . '· `lift` - Make a lift/carry roll (%d)' . PHP_EOL
                        . '· `memory` - Make a memory test (%d)' . PHP_EOL
                        . '· `soak` - Make a soak test (%d)' . PHP_EOL
                        . '· `luck` - Make a luck (edge) test (%d)' . PHP_EOL
                        . '· `init` - Roll your initiative (%dd6+%d)' . PHP_EOL
                        . '· `push 6 [limit] [text]` - Push the limit with 6 + your edge (%d)' . PHP_EOL
                        . '· `blitz` - Blitz initiative (5d6+%8$d)' . PHP_EOL,
                        $character->composure,
                        $character->judge_intentions,
                        $character->lift_carry,
                        $character->memory,
                        $character->soak,
                        $character->edge,
                        $character->initiative_dice,
                        $character->initiative_score,
                        $character->edge,
                    ),
                'color' => TextAttachment::COLOR_INFO,
            ];

            if (null !== $character->resonance) {
                $this->data[] = [
                    'title' => 'Technomancer',
                    'text' => sprintf(
                        '· `fade` - Make a test to resist fading (%d)' . PHP_EOL,
                        $character->resonance + $character->willpower,
                    ),
                    'color' => TextAttachment::COLOR_INFO,
                ];
            }
        } else {
            $this->data[] = [
                'title' => 'Player',
                'text' => 'No character linked' . PHP_EOL
                    . '· `link <characterId>` - Link a character to this '
                    . 'channel' . PHP_EOL
                    . '· `init 12+3d6` - Roll your initiative' . PHP_EOL,
                'color' => TextAttachment::COLOR_INFO,
            ];
        }
    }

    #[Override]
    public function forSlack(): Response
    {
        $response = new Response();
        foreach ($this->data as $element) {
            // @phpstan-ignore method.deprecated
            $response->addAttachment(new TextAttachment(
                $element['title'],
                $element['text'],
                $element['color'],
            ));
        }
        return $response;
    }

    #[Override]
    public function forDiscord(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= sprintf('**%s**', $element['title']) . PHP_EOL
            . $element['text'] . PHP_EOL;
        }
        return $value;
    }

    #[Override]
    public function forIrc(): string
    {
        $value = '';
        foreach ($this->data as $element) {
            $value .= $element['title'] . PHP_EOL
            . $element['text'] . PHP_EOL;
        }
        return $value;
    }
}
