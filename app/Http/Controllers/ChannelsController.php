<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChannelUpdateRequest;
use App\Models\Channel;

class ChannelsController extends Controller
{
    /**
     * Update a channel.
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
