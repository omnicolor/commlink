<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Irc\User;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

use function str_replace;

/**
 * @property IrcClient $client
 * @psalm-suppress PossiblyUnusedProperty
 */
class IrcMessageReceived extends MessageReceived
{
    /**
     * Content of the command, without the preceding ':roll '.
     */
    public string $content;

    public string $server;

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function __construct(
        string $message,
        public User $user,
        public IrcClient $client,
        public IrcChannel $channel,
    ) {
        $this->content = str_replace(':roll ', '', $message);
        $this->server = $client->getConnection()->getServer();
    }
}
