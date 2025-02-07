<?php

declare(strict_types=1);

namespace Modules\Avatar\Enums;

/**
 * @codeCoverageIgnore
 */
enum Background: string
{
    case Military = 'military';
    case Monastic = 'monastic';
    case Outlaw = 'outlaw';
    case Privileged = 'privileged';
    case Urban = 'urban';
    case Wilderness = 'wilderness';

    public function description(): string
    {
        return match ($this) {
            Background::Military => 'You trained to fight as a soldier in a '
                . 'military unit such as a mercenary company, a regional '
                . 'militia, or a state government’s standing army. Are you a '
                . 'soldier, sailor, or spy? Do you still answer to your '
                . 'commanding officer, or have you gone rogue?',
            Background::Monastic => 'You are or were a monk, nun, or acolyte '
                . 'of a particular order. Perhaps you were devoted to finding '
                . 'enlightenment or helping others in a community with other '
                . 'like-minded devotees, or perhaps you were committed to '
                . 'scholastic rituals and bureaucratic traditions. What is '
                . 'your order’s goal? What are its rules? In what ways did '
                . 'your upbringing agree with you, and in what ways did you '
                . 'long for something different?',
            Background::Outlaw => 'You live outside the bounds of law and '
                . 'order as a criminal, insurrectionist, or pirate. Were you '
                . 'born into the lawless life, or did you come into it later '
                . 'on? Did you choose the outlaw life, or did the outlaw life '
                . 'choose you? Do you work alone or with a gang? Whom have you '
                . 'hurt just to stay alive?',
            Background::Privileged => 'You grew up in the lap of luxury, '
                . 'wealth, or prestige as a hereditary aristocrat, prominent '
                . 'merchant, or even the heir to a successful crime family. '
                . 'What advantages did your upbringing give you? Now that '
                . 'you’re no longer surrounded by safety and ease, what do you '
                . 'miss—and what do you fear?',
            Background::Urban => 'You grew up running the streets of a big '
                . 'city like the Northern Water Tribe capital, Yu Dao, or '
                . 'Republic City. You rub shoulders with people from many '
                . 'different walks of life, and you might not feel so at home '
                . 'if your journey takes you to the wilderness. What '
                . 'unexpected skills and knowledge do you have from city life? '
                . 'Which urban amenities do you miss—and which hardships do '
                . 'you not miss?',
            Background::Wilderness => 'You grew up in a town or household '
                . 'surrounded by nature, the elements in their most raw form, '
                . 'and developed advanced survival skills because of it. Which '
                . 'terrain makes you feel at home? What special skill are you '
                . 'most proud of—perhaps orienteering, herbalism, sailing, or '
                . 'animal training? What excites you, and what scares you, '
                . 'about big-city adventures?',
        };
    }
}
