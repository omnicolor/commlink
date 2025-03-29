<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\InteractsWithDiscord;
use App\Models\Traits\InteractsWithSlack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

use function config;
use function sha1;
use function substr;

/**
 * @method static Builder discord()
 * @method static Builder irc()
 * @method static Builder slack()
 * @property-read ?ChatCharacter $chat_character
 * @property int $id
 * @property string $remote_user_id
 * @property ?string $remote_user_name
 * @property string $server_id
 * @property ?string $server_name
 * @property string $server_type
 * @property-read User $user
 * @property int $user_id
 * @property-read string $verification
 * @property bool $verified
 */
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
     * @var list<string>
     */
    protected $fillable = [
        'remote_user_id',
        'remote_user_name',
        'server_id',
        'server_name',
        'server_type',
        'user_id',
        'verified',
    ];

    /**
     * Return the chat character linked to this user.
     */
    public function chatCharacter(): HasOne
    {
        return $this->hasOne(ChatCharacter::class);
    }

    /**
     * Return the verification hash for the user.
     */
    public function verification(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                $hash = sha1(
                    config('app.key') . $this->attributes['server_id']
                        . $this->attributes['remote_user_id']
                        . $this->attributes['user_id']
                );
                return substr($hash, 0, 20);
            },
        );
    }

    /**
     * Scope the query to only include Discord accounts.
     */
    public function scopeDiscord(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_DISCORD);
    }

    public function scopeIrc(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_IRC);
    }

    /**
     * Scope the query to only include Slack accounts.
     */
    public function scopeSlack(Builder $query): Builder
    {
        return $query->where('server_type', self::TYPE_SLACK);
    }

    /**
     * Scope the query to only include unverified users.
     */
    public function scopeUnverified(Builder $query): Builder
    {
        return $query->where('verified', false);
    }

    /**
     * Scope the query to only include verified users.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('verified', true);
    }

    /**
     * Get the user attached to this Chat User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
