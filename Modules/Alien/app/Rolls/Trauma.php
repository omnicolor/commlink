<?php

declare(strict_types=1);

namespace Modules\Alien\Rolls;

use App\Events\MessageReceived;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Slack\TextAttachment;
use App\Rolls\Roll;
use Facades\App\Services\DiceService;

use function sprintf;

use const PHP_EOL;

/**
 * @psalm-suppress UnusedClass
 */
class Trauma extends Roll
{
    /** @var int<1, 6> */
    protected int $roll;

    public function __construct(
        string $content,
        string $username,
        Channel $channel,
        public ?MessageReceived $event = null,
    ) {
        parent::__construct($content, $username, $channel);

        /**
         * @var int<1, 6>
         * @psalm-suppress UndefinedClass
         */
        $roll = DiceService::rollOne(6);

        $this->title = sprintf(
            '%s rolled %d on the permanent mental trauma table',
            $this->username,
            $roll,
        );
        $this->text = match ($roll) {
            1 => 'PHOBIA: You are terrified by something related to what '
                . 'caused you to panic. The GM decides what it is Your STRESS '
                . 'LEVEL increases by one when within SHORT range of the '
                . 'object of your phobia. If you stay close to it for more '
                . 'than a single round, make a Panic Roll.',
            2  => 'ALCOHOLISM: You must drink alcohol every Shift, or your '
                . 'STRESS LEVEL increases by one. You cannot relieve stress '
                . '(see page 104) without drinking alcohol.',
            3 => 'NIGHTMARES: Make an EMPATHY roll when you sleep. If the roll '
                . 'succeeds, you have a horrible nightmare and your STRESS '
                . 'LEVEL increases by one. You cannot relieve stress for a '
                . 'full day after such a nightmare.',
            4 => 'DEPRESSION: You are prone to episodes of depression and '
                . 'moodiness. Every day, make an EMPATHY roll—if you fail, '
                . 'you’re having a bad day. Your STRESS LEVEL increases by one '
                . 'and you can’t relieve stress until the next day.',
            5 => 'DRUG USE: You must use some form of recreational drug (see '
                . 'page 137) every Shift, or your STRESS LEVEL increases by '
                . 'one. You cannot relieve stress (see page 104) without '
                . 'consuming your drug of choice.',
            6 => 'AMNESIA: Your memory is a blank slate. You can no longer '
                . 'recall who you or the other characters are. The effect '
                . 'should be roleplayed.',
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
        $attachment = new TextAttachment(
            $this->title,
            $this->text,
            TextAttachment::COLOR_DANGER,
        );
        $response = new SlackResponse(channel: $this->channel);
        return $response->addAttachment($attachment)->sendToChannel();
    }
}
