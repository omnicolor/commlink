<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\Character;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(
    'App.Models.Shadowrun5e.Character.{id}',
    static function (User $user, string $characterId): bool {
        $character = Character::find($characterId);
        return $user->email === $character->owner;
    },
);

Broadcast::channel(
    'App.Models.User.{id}',
    static function (User $user, string $id): bool {
        return (int)$user->id === (int)$id;
    },
);

Broadcast::channel(
    'users.{userId}',
    static function (User $user, string $userId): bool {
        return $user->id === (int)$userId;
    },
);

Broadcast::channel(
    'campaign.{campaignId}',
    static function (User $user, string $campaignId): bool {
        $campaign = Campaign::findOrFail($campaignId);
        return $user->id === $campaign->gm || $user->id === $campaign->registered_by;
    },
);
