<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('users.{userId}', function (User $user, string $userId): bool {
    return $user->id === (int)$userId;
});

Broadcast::channel('campaign.{campaignId}', function (User $user, string $campaignId): bool {
    $campaign = Campaign::findOrFail($campaignId);
    return $user->id === $campaign->gm || $user->id === $campaign->registered_by;
});
