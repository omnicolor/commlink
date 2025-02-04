<?php

declare(strict_types=1);

namespace Modules\Avatar\Features;

use Override;
use Stringable;

use function implode;

use const PHP_EOL;

class LegacyOfExcellence extends Feature implements Stringable
{
    /** @var array<string, string> */
    public readonly array $drives;

    /**
     * @param array<string, LegacyOfExcellenceDriveStatus> $options
     */
    public function __construct(public readonly array $options)
    {
        $this->drives = [
            'successfully-lead' => 'successfully lead your companions in battle',
            'give-affection' => 'give your affection to someone worthy',
            'start-a-real-fight' => 'start a real fight with a dangerous master',
            'do-justice' => 'do justice to a friend or mentor’s guidance',
            'take-down' => 'take down a dangerous threat all on your own',
            'outperform' => 'openly outperform an authority figure',
            'save-a-friend' => 'save a friend’s life',
            'get-a-new-outfit' => 'get a fancy new outfit',
            'earn-the-respect' => 'earn the respect of an adult you admire',
            'call-out-a-friend' => 'openly call out a friend’s unworthy actions',
            'form-relationship-with-new-master' => 'form a strong relationship with a new master',
            'stop-a-fight' => 'stop a fight with calm words',
            'sacrifice-your-pride' => 'sacrifice your pride or love for a greater good',
            'defend-a-place' => 'defend an inhabited place from dire threats',
            'stand-up-to-disrespect' => 'stand up to someone who doesn’t respect you',
            'make-a-friend-live-up' => 'make a friend live up to a principle they have neglected',
            'show-mercy' => 'show mercy or forgiveness to a dangerous person',
            'stand-up-to-abuse' => 'stand up to someone abusing their power',
            'tame-a-beast' => 'tame or befriend a dangerous beast or rare creature',
            'pull-off-stunt' => 'pull off a ridiculous stunt',
        ];
    }

    #[Override]
    public function __toString(): string
    {
        return 'Legacy of Excellence';
    }

    public function description(): string
    {
        $drives = [];
        foreach ($this->drives as $id => $drive) {
            $status = $this->options[$id] ?? LegacyOfExcellenceDriveStatus::Unfulfilled;
            $drives[] = match ($status) {
                LegacyOfExcellenceDriveStatus::Chosen => '- [x] ' . $drive,
                LegacyOfExcellenceDriveStatus::Fulfilled => '- [x] ~~' . $drive . '~~',
                LegacyOfExcellenceDriveStatus::Unfulfilled => '- [ ] ' . $drive,
            };
        }
        return 'You have dedicated yourself to accomplishing great, exciting '
            . 'deeds and becoming worthy of the trust others place in you. '
            . 'Choose four drives to mark at the start of play. When you '
            . 'fulfill a marked drive, strike it out, and mark growth or '
            . 'clear a condition. When your four marked drives are all struck '
            . 'out, choose and mark four new drives. When all drives are '
            . 'struck out, change playbooks or accept a position of great '
            . 'responsibility and retire from a life of adventure.' . PHP_EOL
            . implode(PHP_EOL, $drives);
    }
}
