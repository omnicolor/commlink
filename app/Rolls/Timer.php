<?php

declare(strict_types=1);

namespace App\Rolls;

use App\Enums\ChannelType;
use App\Events\DiscordMessageReceived;
use App\Jobs\TimerJob;
use App\Models\Channel;
use Carbon\CarbonInterval;
use Omnicolor\Slack\Exceptions\SlackException;
use Omnicolor\Slack\Headers\Header;
use Omnicolor\Slack\Response;
use Omnicolor\Slack\Sections\Markdown;
use Override;
use RuntimeException;

use function explode;
use function is_numeric;
use function sprintf;
use function str_contains;
use function str_replace;

use const PHP_EOL;

/**
 * @see TimerJob
 */
class Timer extends Roll
{
    private ?string $error = null;

    public function __construct(
        string $content,
        string $character,
        Channel $channel,
        public ?DiscordMessageReceived $event = null,
    ) {
        parent::__construct($content, $character, $channel);

        if (ChannelType::Discord === $channel->type && null === $channel->webhook) {
            $this->error = 'Discord webhooks must be set up to use timers';
            return;
        }
        if (ChannelType::Irc === $channel->type) {
            $this->error = 'IRC channels are not supported';
            return;
        }

        $arguments = explode(' ', $content);

        // Shorthand for /roll timer create <whatever>.
        if (is_numeric($arguments[1])) {
            $arguments[2] = $arguments[1];
            $arguments[1] = 'create';
        }

        try {
            match ($arguments[1]) {
                'create' => $this->create($arguments[2] ?? null),
                // TODO: Include showing timers and cancelling them.
                default => throw new RuntimeException(),
            };
        } catch (RuntimeException) {
            $this->error = 'I\'m not sure what that means.';
        }
    }

    /**
     * Creates a new timer job, delayed for some amount of time. When the job
     * finishes, it will notify the user that the timer has completed.
     */
    private function create(null|string $time): void
    {
        if (null === $time) {
            $this->error = 'You didn\'t specify a time.';
            return;
        }

        if (!str_contains($time, ':')) {
            $time = '0:' . $time;
        }
        $interval = CarbonInterval::createFromFormat('H:i', $time);

        $this->title = 'Timer created';
        $this->text = sprintf(
            'Ok %s, I\'ll let you know when %s is up.',
            $this->channel->user,
            $interval,
        );

        TimerJob::dispatch($this->channel, $interval, $this->channel->user)
            ->delay($interval);
    }

    #[Override]
    public function forDiscord(): string
    {
        if (null !== $this->error) {
            return $this->error;
        }

        return sprintf('**%s**', $this->title) . PHP_EOL . str_replace(
            $this->channel->user,
            $this->channel->username,
            $this->text,
        );
    }

    #[Override]
    public function forIrc(): string
    {
        return $this->error ?? 'Error';
    }

    #[Override]
    public function forSlack(): Response
    {
        if (null !== $this->error) {
            throw new SlackException($this->error);
        }

        return (new Response())
            ->addBlock(new Header($this->title))
            ->addBlock(new Markdown(str_replace(
                $this->channel->user,
                sprintf('<@%s>', $this->channel->user),
                $this->text,
            )))
            ->sendToChannel();
    }
}
