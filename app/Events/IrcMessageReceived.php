<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;

/**
 * @psalm-suppress PossiblyUnusedProperty
 */
class IrcMessageReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * Content of the command, without the preceding ':roll '.
     * @var string
     */
    public string $content;

    public function __construct(
        public string $message,
        public string $user,
        public IrcClient $client,
        public IrcChannel $channel,
    ) {
        $this->content = \str_replace(':roll ', '', $message);
    }
}
