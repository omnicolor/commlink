<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\InteractsWithDiscord;
use App\Models\Traits\InteractsWithSlack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatUser extends Model
{
    use HasFactory;
    use InteractsWithDiscord;
    use InteractsWithSlack;

    public const TYPE_DISCORD = 'discord';
    public const TYPE_IRC = 'irc';
    public const TYPE_SLACK = 'slack';

    public const VALID_TYPES = [
        self::TYPE_DISCORD,
        self::TYPE_IRC,
        self::TYPE_SLACK,
    ];

    /**
     * Mass assignable properties.
     * @var array<int, string>
     */
    protected $fillable = [
        'server_id',
        'server_name',
        'server_type',
        'remote_user_id',
        'remote_user_name',
        'user_id',
        'verified',
    ];

    /**
     * Return the chat character linked to this user.
     * @return HasOne
     */
    public function chatCharacter(): HasOne
    {
        return $this->hasOne(ChatCharacter::class);
    }

    /**
     * Return the verification hash for the user.
     * @return string
     */
    public function getVerificationAttribute(): string
    {
        $hash = \sha1(
            config('app.key') . $this->attributes['server_id']
                . $this->attributes['remote_user_id']
                . $this->attributes['user_id']
        );
        return \substr($hash, 0, 20);
    }

    /**
     * Scope the query to only include Discord accounts.
     * @param Builder $query
     * @return Builder
     */
    public function scopeDiscord(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_DISCORD);
    }

    /** @psalm-suppress PossiblyUnusedMethod */
    public function scopeIrc(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_IRC);
    }

    /**
     * Scope the query to only include Slack accounts.
     * @param Builder $query
     * @return Builder
     */
    public function scopeSlack(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_SLACK);
    }

    /**
     * Scope the query to only include unverified users.
     * @param Builder $query
     * @return Builder
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('verified', false);
    }

    /**
     * Scope the query to only include verified users.
     * @param Builder $query
     * @return Builder
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Get the user attached to this Chat User.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
