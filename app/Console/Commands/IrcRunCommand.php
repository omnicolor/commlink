<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\IrcMessageReceived;
use App\Models\Irc\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Jerodev\PhpIrcClient\IrcChannel;
use Jerodev\PhpIrcClient\IrcClient;
use Jerodev\PhpIrcClient\Options\ClientOptions;
use Spatie\SignalAwareCommand\SignalAwareCommand;

use function sprintf;

/**
 * Start an IRC bot.
 * @codeCoverageIgnore
 * @psalm-suppress UnusedClass
 */
class IrcRunCommand extends SignalAwareCommand
{
    protected IrcClient $client;

    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Start the IRC bot server';

    /**
     * Nickname to use for the IRC bot.
     */
    protected string $nickname;

    /**
     * Port to connect to.
     */
    protected string $port;

    /**
     * IRC server's hostname.
     */
    protected string $server;

    /**
     * @var array<string, User>
     */
    protected array $users = [];

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:irc-run';

    public function __construct()
    {
        $this->signature = 'commlink:irc-run' . \PHP_EOL
            . '{server : Hostname of the server to connect to}' . \PHP_EOL
            . '{--port=6667 : Port to connect to}' . \PHP_EOL
            . '{--nickname=' . config('app.name') . ' : Nickname to use in IRC}' . \PHP_EOL
            . '{--channel=* : Channel(s) to automatically join}';
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return int
     */
    public function handle(): int
    {
        $this->server = $this->argument('server');
        $this->port = $this->option('port') ?? '6667';
        if (
            null !== $this->option('nickname')
            && '' !== trim($this->option('nickname'))
        ) {
            $this->nickname = trim($this->option('nickname'));
        } else {
            $this->nickname = config('app.name');
        }
        $channels = $this->option('channel');
        $options = new ClientOptions(
            nickname: $this->nickname,
            channels: array_filter($channels),
        );
        $options->autoRejoin = true;

        $this->client = new IrcClient(
            sprintf('%s:%s', $this->server, $this->port),
            $options,
        );

        $this->client->on('kick', [$this, 'handleKick']);
        $this->client->on('invite', [$this, 'handleInvite']);
        $this->client->on('message', [$this, 'handleMessage']);
        $this->client->on('mode', [$this, 'handleMode']);
        $this->client->on('motd', [$this, 'handleMotd']);
        $this->client->on('registered', [$this, 'handleRegistration']);
        $this->client->on('names', [$this, 'handleNames']);
        $this->client->on('nick-is-registered', [$this, 'handleWhois']);
        $this->client->on('renamed', [$this, 'handleRename']);

        $this->client->connect();

        return self::SUCCESS;
    }

    public function handleWhois(string $user): void
    {
        $this->users[$user]->registered = true;
    }

    public function handleRename(string $oldNick, string $newNick): void
    {
        $this->client->send('WHOIS ' . $newNick);
        $this->users[$newNick] = $this->users[$oldNick] ?? new User(nick: $newNick);
        unset($this->users[$oldNick]);
    }

    /**
     * @param array<int, string> $names
     * @psalm-suppress PossiblyUnusedParam
     */
    public function handleNames(IrcChannel $channel, array $names): void
    {
        foreach ($names as $name) {
            $user = new User(nick: $name);
            if (Str::startsWith($name, '@')) {
                $user->op = true;
                $user->nick = Str::replaceStart('@', '', $name);
            }
            if (Str::startsWith($name, '+')) {
                $user->voice = true;
                $user->nick = Str::replaceStart('+', '', $name);
            }
            $this->users[$name] = $user;
            $this->client->send('WHOIS ' . $name);
        }
    }

    public function handleInvite(IrcChannel $channel, string $user): void
    {
        Log::info(
            'IRC - Bot was invited to {channel} by {user}',
            [
                'channel' => $channel->getName(),
                'user' => $user,
            ]
        );
        $this->line(sprintf(
            'Bot was invited to %s by %s',
            $channel->getName(),
            $user
        ));
        $this->client->join($channel->getName());
    }

    public function handleKick(
        IrcChannel $channel,
        string $user,
        string $kicker,
        string $message
    ): void {
        Log::info(
            'IRC - {user} was kicked from {channel} by {kicker}',
            [
                'channel' => optional($channel)->getName(),
                'kicker' => $kicker,
                'message' => $message,
                'user' => $user,
            ]
        );
        $this->line(sprintf(
            'Bot was kicked from %s by %s (%s)',
            $channel->getName(),
            $kicker,
            $message
        ));
    }

    public function handleMessage(
        string $from,
        IrcChannel $channel,
        string $message
    ): void {
        if (':roll' !== substr($message, 0, 5)) {
            // Ignore non-colon messages.
            Log::debug(
                'IRC - Ignoring message',
                [
                   'channel' => $channel->getName(),
                   'user' => $from,
                   'message' => $message,
                ]
            );
            return;
        }

        $this->line(sprintf(
            '%10s - %12s - %s',
            $channel->getName(),
            $from,
            $message,
        ));
        Log::debug(
            'IRC - Handled message',
            [
                'channel' => $channel->getName(),
                'user' => $from,
                'message' => $message,
            ]
        );

        IrcMessageReceived::dispatch(
            $message,
            $this->users[$from] ?? new User(nick: $from),
            $this->client,
            $channel
        );
    }

    public function handleMode(
        ?IrcChannel $channel,
        string $user,
        string $mode
    ): void {
        Log::debug(
            'IRC - Mode {mode} to {user} in {channel}',
            [
                'channel' => optional($channel)->getName(),
                'mode' => $mode,
                'user' => $user,
            ]
        );
    }

    public function handleMotd(string $motd): void
    {
        Log::debug('IRC - Message of the day', ['motd' => $motd]);
    }

    public function handleRegistration(): void
    {
        $this->line(sprintf(
            'Connected to %s, port %s',
            $this->server,
            $this->port
        ));
        Log::info(
            'IRC - Connected to {server}:{port}',
            [
                'server' => $this->server,
                'port' => $this->port,
            ]
        );
    }

    /**
     * Operator pressed CTRL-C.
     */
    public function onSigint(): void
    {
        $this->line('CTRL-C detected');
        $this->disconnect();
    }

    /**
     * Operator killed the process.
     */
    public function onSigterm(): void
    {
        $this->line('Kill signal detected');
        $this->disconnect();
    }

    public function onSigtstp(): void
    {
        $this->line('Ignoring CTRL-Z');
    }

    protected function disconnect(): void
    {
        foreach ($this->client->getChannels() as $channel) {
            $this->line('Leaving ' . $channel->getName());
            $this->client->part($channel->getName());
        }
        $this->client->disconnect();
        $message = sprintf('Disconnecting from %s', $this->server);
        $this->line($message);
        Log::info($message);
    }
}
