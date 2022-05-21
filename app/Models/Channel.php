<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\Traits\InteractsWithSlack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property ?string $system
 */
class Channel extends Model
{
    use GameSystem;
    use HasFactory;
    use InteractsWithDiscord;
    use InteractsWithSlack;

    public const TYPE_DISCORD = 'discord';
    public const TYPE_SLACK = 'slack';

    public const VALID_TYPES = [
        self::TYPE_DISCORD,
        self::TYPE_SLACK,
    ];

    /**
     * Attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'channel_id',
        'channel_name',
        'registered_by',
        'server_id',
        'server_name',
        'system',
        'type',
        'webhook',
    ];

    public string $user = '';
    public string $username = 'Unknown';

    /**
     * Return the campaign attached to the channel.
     * @return BelongsTo
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Return the character linked to this user and channel.
     * @return ?Character
     */
    public function character(): ?Character
    {
        $chatUser = $this->getChatUser();
        if (null === $chatUser) {
            return null;
        }
        $chatCharacter = ChatCharacter::where('channel_id', $this->id)
            ->where('chat_user_id', $chatUser->id)
            ->first();
        if (null === $chatCharacter) {
            return null;
        }
        return $chatCharacter->getCharacter();
    }

    /**
     * Get the chat user linked to this server.
     * @return ?ChatUser
     */
    public function getChatUser(): ?ChatUser
    {
        return ChatUser::verified()
            ->where('remote_user_id', $this->user)
            ->where('server_id', $this->server_id)
            ->where('server_type', $this->type)
            ->first();
    }

    /**
     * Return the server's name.
     * @return ?string
     */
    public function getServerNameAttribute(): ?string
    {
        // If we've already retrieved it, just return what we've got.
        if (null !== ($this->attributes['server_name'] ?? null)) {
            return $this->attributes['server_name'];
        }

        switch ($this->attributes['type'] ?? null) {
            case self::TYPE_DISCORD:
                $this->attributes['server_name'] = self::getDiscordServerName(
                    $this->attributes['server_id']
                );
                if (isset($this->id)) {
                    $this->save();
                }
                return $this->attributes['server_name'];
            case self::TYPE_SLACK:
                $this->attributes['server_name']
                    = (string)self::getSlackTeamName($this->attributes['server_id']);
                if (isset($this->id)) {
                    $this->save();
                }
                return $this->attributes['server_name'];
            default:
                return null;
        }
    }

    /**
     * Return the initiatives rolled for the channel.
     * @return HasMany
     */
    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }

    /**
     * Scope the query to only include Discord accounts.
     * @param Builder $query
     * @return Builder
     */
    public function scopeDiscord(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_DISCORD);
    }

    /**
     * Scope the query to only include Slack accounts.
     * @param Builder $query
     * @return Builder
     */
    public function scopeSlack(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SLACK);
    }

    /**
     * Set the system for the channel.
     * @param string $system
     * @throws \RuntimeException
     */
    public function setSystemAttribute(string $system): void
    {
        if (!\array_key_exists($system, config('app.systems'))) {
            throw new \RuntimeException('Invalid system');
        }
        $this->attributes['system'] = $system;
    }

    /**
     * Set the type of server for the channel.
     * @param string $type
     * @throws \RuntimeException
     */
    public function setTypeAttribute(string $type): void
    {
        if (!\in_array($type, self::VALID_TYPES, true)) {
            throw new \RuntimeException('Invalid channel type');
        }
        $this->attributes['type'] = $type;
    }
}
