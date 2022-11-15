<?php

declare(strict_types=1);

namespace App\Listeners\Shadowrun5e;

use App\Events\Shadowrun5e\DamageEvent;
use App\Http\Responses\Slack\SlackResponse;
use App\Models\Channel;
use App\Models\Shadowrun5e\Character;
use App\Models\Slack\TextAttachment;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use stdClass;

class HandleDamageEvent
{
    use SerializesModels;

    public function handle(DamageEvent $event): void
    {
        $campaign = $event->campaign;
        foreach ($event->campaign->channels ?? [] as $channel) {
            if ('slack' === $channel->type) {
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
        stdClass $damage
    ): string {
        return \sprintf(
            'Stun: %d Physical: %d Overflow: %d',
            ($character->damageStun ?? 0) + $damage->stun,
            ($character->damagePhysical ?? 0) + $damage->physical,
            ($character->damageOverflow ?? 0) + $damage->overflow,
        );
    }

    protected function formatMessage(
        Character $character,
        stdClass $damage
    ): string {
        if (0 !== $damage->stun) {
            if (0 === $damage->physical && 0 === $damage->overflow) {
                return \sprintf(
                    '%s takes %d point%s of stun',
                    $character,
                    $damage->stun,
                    1 === $damage->stun ? '' : 's',
                );
            }
            if (0 === $damage->overflow) {
                return \sprintf(
                    '%s fills their stun track with %d points and overflows '
                        . 'into %d physical damage',
                    $character,
                    $damage->stun,
                    $damage->physical,
                );
            }
            return \sprintf(
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
                return \sprintf(
                    '%s fills their physical track with %d point%s and takes '
                        . '%d point%s of overflow',
                    $character,
                    $damage->physical,
                    1 === $damage->physical ? '' : 's',
                    $damage->overflow,
                    1 === $damage->overflow ? '' : 's',
                );
            }
            return \sprintf(
                '%s takes %d point%s of physical',
                $character,
                $damage->physical,
                1 === $damage->physical ? '' : 's',
            );
        }
        return \sprintf(
            '%s takes %d point%s of overflow',
            $character,
            $damage->overflow,
            1 === $damage->overflow ? '' : 's',
        );
    }

    protected function sendToSlack(
        Character $character,
        stdClass $damage,
        Channel $channel
    ): void {
        $data = new SlackResponse(channel: $channel);
        $attachment = new TextAttachment(
            'Damage!',
            $this->formatMessage($character, $damage),
            TextAttachment::COLOR_DANGER,
        );
        $attachment->addFooter($this->formatFooter($character, $damage));
        $data->addAttachment($attachment);
        $data = $data->getData();
        $data->response_type = null;
        $data->channel = $channel->channel_id;

        // TODO: Add error handling.
        Http::withHeaders([
            'Authorization' => \sprintf('Bearer %s', config('app.slack_token')),
        ])->post('https://slack.com/api/chat.postMessage', (array)$data);
    }

    protected function sendToDiscord(
        Character $character,
        stdClass $damage,
        Channel $channel
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
