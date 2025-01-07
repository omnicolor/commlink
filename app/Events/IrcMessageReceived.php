<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Irc\User;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

use function str_replace;

final class IrcMessageReceived extends MessageReceived
{
    /**
     * Content of the command, without the preceding ':roll '.
     */
    public readonly string $content;

    public readonly string $server;

    public function __construct(
        string $message,
        public readonly User $user,
        public readonly IrcClient $client,
        public readonly IrcChannel $channel,
    ) {
        $this->content = str_replace(':roll ', '', $message);
        $this->server = $client->getConnection()->getServer();
    }
}
