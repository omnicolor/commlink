<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Listeners;

use App\Enums\ChannelType;
use App\Models\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Modules\Shadowrun5e\Events\DamageEvent;
use Modules\Shadowrun5e\Models\Character;
use Omnicolor\Slack\Attachments\TextAttachment;
use Omnicolor\Slack\Response;
use stdClass;

use function sprintf;

class HandleDamageEvent
{
    use SerializesModels;

    public function handle(DamageEvent $event): void
    {
        /** @var Channel $channel */
        foreach ($event->campaign->channels ?? [] as $channel) {
            if (ChannelType::Slack === $channel->type) {
                $this->sendToSlack($event->character, $event->damage, $channel);
                continue;
            }
            if (null === $channel->webhook) {
                // We can't broadcast to Discord channels without webhooks.
                continue;
            }
            $this->sendToDiscord($event->character, $event->damage, $channel);
        }
    }

    protected function formatFooter(
        Character $character,
        stdClass $damage,
    ): string {
        return sprintf(
            'Stun: %d Physical: %d Overflow: %d',
            ($character->damageStun ?? 0) + $damage->stun,
            ($character->damagePhysical ?? 0) + $damage->physical,
            ($character->damageOverflow ?? 0) + $damage->overflow,
        );
    }

    protected function formatMessage(
        Character $character,
        stdClass $damage,
    ): string {
        if (0 !== $damage->stun) {
            if (0 === $damage->physical && 0 === $damage->overflow) {
                return sprintf(
                    '%s takes %d point%s of stun',
                    $character,
                    $damage->stun,
                    1 === $damage->stun ? '' : 's',
                );
            }
            if (0 === $damage->overflow) {
                return sprintf(
                    '%s fills their stun track with %d points and overflows '
                        . 'into %d physical damage',
                    $character,
                    $damage->stun,
                    $damage->physical,
                );
            }
            return sprintf(
                '%s fills their stun track (%d point%s) and their physical '
                    . 'track (%d), taking %d point%s of overflow',
                $character,
                $damage->stun,
                1 === $damage->stun ? '' : 's',
                $damage->physical,
                $damage->overflow,
                1 === $damage->overflow ? '' : 's',
            );
        }
        if (0 !== $damage->physical) {
            if (0 !== $damage->overflow) {
                return sprintf(
                    '%s fills their physical track with %d point%s and takes '
                        . '%d point%s of overflow',
                    $character,
                    $damage->physical,
                    1 === $damage->physical ? '' : 's',
                    $damage->overflow,
                    1 === $damage->overflow ? '' : 's',
                );
            }
            return sprintf(
                '%s takes %d point%s of physical',
                $character,
                $damage->physical,
                1 === $damage->physical ? '' : 's',
            );
        }
        return sprintf(
            '%s takes %d point%s of overflow',
            $character,
            $damage->overflow,
            1 === $damage->overflow ? '' : 's',
        );
    }

    protected function sendToSlack(
        Character $character,
        stdClass $damage,
        Channel $channel,
    ): void {
        $attachment = (new TextAttachment(
            'Damage!',
            $this->formatMessage($character, $damage),
            TextAttachment::COLOR_DANGER,
        ))
            ->addFooter($this->formatFooter($character, $damage));

        // @phpstan-ignore method.deprecated
        $response = (new Response())
            ->addAttachment($attachment)
            ->jsonSerialize();
        $response['response_type'] = null;
        $response['channel'] = $channel->channel_id;

        // TODO: Add error handling.
        Http::withHeaders([
            'Authorization' => sprintf('Bearer %s', config('app.slack_token')),
        ])->post('https://slack.com/api/chat.postMessage', $response);
    }

    protected function sendToDiscord(
        Character $character,
        stdClass $damage,
        Channel $channel,
    ): void {
        $data = [
            'content' => $this->formatMessage($character, $damage)
                . ' (' . $this->formatFooter($character, $damage) . ')',
        ];

        /** @var string */
        $url = $channel->webhook;

        // TODO: Add error handling.
        Http::withHeaders([
            'Authorization' => sprintf('Bot %s', config('discord_token')),
        ])->post($url, $data);
    }
}
