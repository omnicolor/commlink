<?php

declare(strict_types=1);

namespace Modules\Avatar\Features;

use Override;
use Stringable;

use function sprintf;

class ProtectorsBurden extends Feature implements Stringable
{
    public readonly string $burden;

    /**
     * @param array{burden?: string} $options
     */
    public function __construct(array $options)
    {
        $this->burden = $options['burden'] ?? 'Unknown';
    }

    #[Override]
    public function __toString(): string
    {
        return 'Protector’s Burden';
    }

    public function description(): string
    {
        $description = <<<'DESC'
            You take it upon yourself to protect the people around you in
            general, but you have someone in particular you keep safe.

            **Name your ward:** %s

            When they mark a condition in front of you, mark fatigue or a
            condition. Your ward can always **call on you to live up to your
            principle**—without shifting their balance away from center—and
            they take +1 to do it.

            At the beginning of each session, roll, taking +1 for each yes:
            - Do you believe your ward listens to you more often than not?
            - Have you recently protected them or helped them with a problem?
            - Is there an immediate threat to your ward that you are aware of?

            On a 7–9, hold 1. On a 10+, hold 2. At any time, spend the hold to:
            - Take a 10+ without rolling on any move to defend or protect them
            - Track them down even if they are hidden or avoiding you
            - Figure out what they’re up to without them knowing

            On a miss, hold 1, but…you’re drifting apart on different paths. By
            the end of the session, you must choose one:
            - Decide you’re the only one who can keep them safe; shift your
            balance twice toward Self-Reliance and keep them as your ward
            - Decide they can handle life without your protection; shift your
            balance twice toward Trust and switch your ward to a new person

            You may also switch your ward if they leave play or are no longer
            present for some reason. When you switch your ward, you can switch
            to an NPC (if the GM agrees).
            DESC;
        return sprintf($description, $this->burden);
    }
}
