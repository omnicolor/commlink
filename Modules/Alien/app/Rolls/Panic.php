<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Events\MessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function is_numeric;
use function sprintf;
use function trim;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Panic extends Roll
{
    /** @var int<1, max> */
    protected int $result;

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?MessageReceived $event = null,
    ) {
        parent::__construct($content, $username, $channel);

        /** @psalm-suppress UndefinedClass */
        $roll = DiceService::rollOne(6);

        $stress = 0;
        $args = explode(' ', trim($content));
        if (isset($args[1]) && is_numeric($args[1])) {
            $stress = (int)$args[1];
        }
        // @phpstan-ignore assign.propertyType
        $this->result = $roll + $stress;

        $this->title = sprintf(
            '%s rolled %d+%d=%d on the panic roll table',
            $this->username,
            $roll,
            $stress,
            $this->result,
        );
        $this->text = match ($this->result) {
            1, 2, 3, 4, 5, 6 => 'KEEPING IT TOGETHER. You manage to keep your '
                . 'nerves in check. Barely.',
            7 => 'NERVOUS TWITCH. Your STRESS LEVEL, and the STRESS LEVEL of '
                . 'all friendly PCs in SHORT range of you, increases by one.',
            8 => 'TREMBLE. You start to tremble uncontrollably. All skill '
                . 'rolls using AGILITY suffer a –2 modification until your '
                . 'panic stops.',
            9 => 'DROP ITEM. Whether by stress, confusion or the realization '
                . 'that you’re all going to die anyway, you drop a weapon or '
                . 'other important item—the GM decides which one. Your STRESS '
                . 'LEVEL increases by one.',
            10 => 'FREEZE. You’re frozen by fear or stress for one Round, '
                . 'losing your next slow action. Your STRESS LEVEL, and the '
                . 'STRESS LEVEL of all friendly PCs in SHORT range of you, '
                . 'increases by one.',
            11 => 'SEEK COVER. You must use your next action to move away from '
                . 'danger and find a safe spot if possible. You are allowed to '
                . 'make a retreat roll (see page 93) if you have an enemy at '
                . 'ENGAGED range. Your STRESS LEVEL is decreased by one, but '
                . 'the STRESS LEVEL of all friendly PCs in SHORT range '
                . 'increases by one. After one Round, you can act normally.',
            12 => 'SCREAM. You scream your lungs out for one Round, losing '
                . 'your next slow action. Your STRESS LEVEL is decreased by '
                . 'one, but every friendly character who hears your scream '
                . 'must make an immediate Panic Roll.',
            13 => 'FLEE. You just can’t take it anymore. You must flee to a '
                . 'safe place and refuse to leave it. You won’t attack anyone '
                . 'and won’t attempt anything dangerous. You are not allowed '
                . 'to make a retreat roll (see page 93) if you have an enemy '
                . 'at ENGAGED range when you flee. Your STRESS LEVEL is '
                . 'decreased by one, but every friendly character who sees you '
                . 'run must make an immediate Panic Roll.',
            14 => 'BERSERK. You must immediately attack the nearest person or '
                . 'creature, friendly or not. You won’t stop until you or the '
                . 'target is Broken. Every friendly character who witnesses '
                . 'your rampage must make an immediate Panic Roll.',
            default => 'CATATONIC. You collapse to the floor and can’t talk or '
                    . 'move, staring blankly into oblivion.',
        };
    }

    public function forDiscord(): string
    {
        return sprintf('**%s**', $this->title) . PHP_EOL . $this->text;
    }

    public function forIrc(): string
    {
        return $this->title . PHP_EOL . $this->text;
    }

    public function forSlack(): SlackResponse
    {
        $color = TextAttachment::COLOR_DANGER;
        if (7 > $this->result) {
            $color = TextAttachment::COLOR_SUCCESS;
        }
        $attachment = new TextAttachment($this->title, $this->text, $color);
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }
}
