<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Event $event
 * @property int $event_id
 * @property string $response
 * @property User $user
 * @property int $user_id
 */
class EventRsvp extends Model
{
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'event_id',
        'response',
        'user_id',
    ];

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
