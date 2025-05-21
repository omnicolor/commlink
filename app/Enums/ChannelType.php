<?php

declare(strict_types=1);

namespace App\Enums;

enum ChannelType: string
{
    case Discord = 'discord';
    case Irc = 'irc';
    case Slack = 'slack';
}
