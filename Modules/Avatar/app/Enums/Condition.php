<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

/**
 * @codeCoverageIgnore
 */
enum Condition: string
{
    case Afraid = 'afraid';
    case Angry = 'angry';
    case Guilty = 'guilty';
    case Insecure = 'insecure';
    case Troubled = 'troubled';

    public function description(): string
    {
        return match ($this) {
            Condition::Afraid => '-2 to intimidate and call someone out',
            Condition::Angry => '-2 to guide and comfort and assess a situation',
            Condition::Guilty => '-2 to push your luck and +2 to deny a callout',
            Condition::Insecure => '-2 to trick and resist shifting your balance',
            Condition::Troubled => '-2 to plead and rely on your skills or training',
        };
    }
}
