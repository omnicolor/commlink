<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ChannelDeleteRequest;
use App\Http\Requests\ChannelUpdateRequest;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;

class ChannelsController extends Controller
{
    public function destroy(
        ChannelDeleteRequest $request,
        Channel $channel,
    ): JsonResponse {
        $channel->delete();
        return new JsonResponse('', JsonResponse::HTTP_NO_CONTENT);
    }

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
