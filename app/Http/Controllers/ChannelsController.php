<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChannelUpdateRequest;
use App\Models\Channel;

/**
 * @psalm-suppress UnusedClass
 */
class ChannelsController extends Controller
{
    /**
     * Update a channel.
     * @param ChannelUpdateRequest $request
     * @param Channel $channel
     * @return Channel
     */
    public function update(ChannelUpdateRequest $request, Channel $channel): Channel
    {
        if ($request->has('auto')) {
            $webhook = $channel->createDiscordWebhook($channel->channel_id);
        } else {
            $webhook = $request->input('webhook');
        }
        $channel->webhook = $webhook;
        $channel->save();
        return $channel;
    }
}
