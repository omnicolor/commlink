<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Events\DiscordMessageReceived;
use App\Models\Channel;
use App\Models\Event as CampaignEvent;
use App\Models\EventRsvp;
use App\Policies\EventPolicy;
use Discord\Discord;
use Discord\Parts\Channel\Message;
use Discord\Parts\WebSockets\MessageReaction;
use Discord\WebSockets\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use function substr;

/**
 * Start a Discord bot.
 * @codeCoverageIgnore
 */
class DiscordRunCommand extends Command
{
    /**
     * The console command description.
     * @var ?string
     */
    protected $description = 'Start the Discord bot server';

    /**
     * The name and signature of the console command.
     * @var string
     */
    protected $signature = 'commlink:discord-run';

    protected string $id;
    protected string $token;

    /**
     * Collection of messages containing reactions that we're monitoring.
     *
     * Key: Message ID, Value: Message footer, containing the response type
     * (such as RSVP), a colon character (:), and optionally any arguments
     * needed (such as an event ID for an RSVP).
     * @deprecated Will be moved to Redis
     * @var array<string, string>
     */
    protected array $messages = [];

    public function handle(): int
    {
        $this->token = config('services.discord.token');
        $discord = new Discord([
            'logger' => Log::getLogger(),
            'storeMessages' => true,
            'token' => $this->token,
        ]);

        $discord->on('ready', function (Discord $discord): void {
            $this->info(sprintf(
                'Logged in to Discord as "%s" (%s)',
                $discord->user->username,
                $discord->user->id,
            ));
            $this->id = $discord->user->id;
        });
        $discord->on(
            Event::MESSAGE_CREATE,
            function (Message $message, Discord $discord): void {
                // @phpstan-ignore-next-line
                if ($message->author->bot) {
                    $this->handleBotMessages($message);
                    return;
                }
                if ('/' !== substr($message->content, 0, 1)) {
                    // Ignore non-command chatter.
                    return;
                }
                DiscordMessageReceived::dispatch($message, $discord);
            }
        );
        $discord->on(
            Event::MESSAGE_REACTION_ADD,
            [$this, 'handleMessageReaction'],
        );

        $discord->run();
        return self::SUCCESS;
    }

    protected function handleBotMessages(Message $message): void
    {
        if (null === $message->webhook_id || null === $message->guild_id) {
            return;
        }
        $channel = Channel::findForWebhook(
            $message->guild_id,
            $message->webhook_id,
        );
        if (null === $channel) {
            return;
        }
        $footer = $message->embeds[0]?->footer?->text;
        if (null === $footer) {
            return;
        }
        if (!str_contains($footer, ':')) {
            return;
        }

        // For now we'll be lazy and hard code in the only interaction
        // supported.
        [$action, $id] = explode(':', $footer);
        if ('rsvp' !== $action || !is_numeric($id)) {
            return;
        }

        // TODO: Move messages store into Redis.
        $this->messages[$message->id] = $footer;
        $message->react('👍');
        $message->react('👎');
        $message->react('🤷');
    }

    public function handleMessageReaction(MessageReaction $reaction): void
    {
        if ($reaction->user_id === $this->id) {
            // Ignore reactions this bot adds.
            return;
        }
        $user = $reaction->member?->username;

        $channel = Channel::discord()
            ->where('channel_id', $reaction->channel_id)
            ->where('server_id', $reaction->guild_id)
            ->first();
        if (null === $channel) {
            return;
        }

        $channel->user = (string)$reaction->user_id;
        $chatUser = $channel->getChatUser();
        if (null === $chatUser) {
            return;
        }

        $message_id = $reaction->message_id;
        if (!array_key_exists($message_id, $this->messages)) {
            return;
        }

        [, $event_id] = explode(':', $this->messages[$message_id]);
        $event = CampaignEvent::find($event_id);
        if (null === $event) {
            return;
        }

        if (!(new EventPolicy())->view($chatUser->user, $event)) {
            return;
        }

        $response = $reaction->emoji->name;
        if (!in_array($response, ['👍', '👎', '🤷'], true)) {
            return;
        }
        $response = match ($response) {
            '👍' => 'accepted',
            '👎' => 'declined',
            '🤷' => 'tentative',
        };
        EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $chatUser->user?->id],
            ['response' => $response],
        );

        $content = match ($response) {
            'accepted' => sprintf(
                '%s **will** attend the event "%s"',
                $user,
                $event->name,
            ),
            'declined' => sprintf(
                '%s **will not** attend the event "%s"',
                $user,
                $event->name,
            ),
            'tentative' => sprintf(
                '%s has not decided whether they\'ll attend the event "%s"',
                $user,
                $event->name,
            ),
        };

        Http::withHeaders(['Authorization' => sprintf('Bot %s', $this->token)])
            ->post(
                sprintf(
                    'https://discord.com/api/channels/%s/messages',
                    $reaction->channel_id,
                ),
                [
                    'content' => $content,
                    'message_reference' => [
                        'channel_id' => $reaction->channel_id,
                        'guild_id' => $reaction->guild_id,
                        'message_id' => $reaction->message_id,
                        'fail_if_not_exists' => true,
                    ],
                ],
            );
    }
}
