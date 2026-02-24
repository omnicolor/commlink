<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ChannelType;
use App\Models\Traits\GameSystem;
use App\Models\Traits\InteractsWithDiscord;
use App\Models\Traits\InteractsWithSlack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Override;
use RuntimeException;

use function array_key_exists;
use function config;
use function sprintf;

/**
 * Representation of a Slack, Discord, or IRC channel.
 * @method static Builder discord()
 * @method static Builder irc()
 * @method static Builder slack()
 * @property-read ?Campaign $campaign
 * @property ?int $campaign_id
 * @property string $channel_id
 * @property string $channel_name
 * @property-read int $id
 * @property ?int $registered_by
 * @property string $server_id
 * @property ?string $server_name
 * @property ?string $system
 * @property ChannelType $type
 * @property ?string $webhook
 */
class Channel extends Model
{
    use GameSystem;
    use HasFactory;
    use InteractsWithDiscord;
    use InteractsWithSlack;

    /**
     * @var list<string>
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
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @return array<string, class-string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'type' => ChannelType::class,
        ];
    }

    /**
     * Return the character linked to this user and channel.
     */
    public function character(): ?Character
    {
        $chatUser = $this->getChatUser();
        if (!$chatUser instanceof ChatUser) {
            return null;
        }
        return ChatCharacter::where('channel_id', $this->id)
            ->where('chat_user_id', $chatUser->id)
            ->first()
            ?->getCharacter();
    }

    /**
     * Get the chat user linked to this server.
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
     * Get the characters registered to this channel.
     * @return array<int, Character>
     */
    public function characters(): array
    {
        $characters = [];
        $chatCharacters = ChatCharacter::where('channel_id', $this->id)->get();
        foreach ($chatCharacters as $chatCharacter) {
            $character = $chatCharacter->getCharacter();
            if (null === $character) {
                continue;
            }
            $characters[] = $character;
        }
        return $characters;
    }

    /**
     * Return the server's name.
     */
    public function serverName(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                // If we've already retrieved it, just return what we've got.
                if (null !== ($this->attributes['server_name'] ?? null)) {
                    return $this->attributes['server_name'];
                }

                switch ($this->type ?? null) {
                    case ChannelType::Discord:
                        $this->attributes['server_name'] = self::getDiscordServerName(
                            $this->attributes['server_id']
                        );
                        if (isset($this->id)) {
                            $this->save();
                        }
                        return $this->attributes['server_name'];
                    case ChannelType::Slack:
                        $this->attributes['server_name']
                            = (string)self::getSlackTeamName($this->attributes['server_id']);
                        if (isset($this->id)) {
                            $this->save();
                        }
                        return $this->attributes['server_name'];
                    default:
                        return null;
                }
            },
        );
    }

    /**
     * Return the initiatives rolled for the channel.
     */
    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Scope the query to only include Discord accounts.
     */
    public function scopeDiscord(Builder $query): Builder
    {
        return $query->where('type', ChannelType::Discord);
    }

    /**
     * Scope the query to only include IRC accounts.
     */
    public function scopeIrc(Builder $query): Builder
    {
        return $query->where('type', ChannelType::Irc);
    }

    /**
     * Scope the query to only include Slack accounts.
     */
    public function scopeSlack(Builder $query): Builder
    {
        return $query->where('type', ChannelType::Slack);
    }

    /**
     * Set the system for the channel.
     */
    public function system(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                return $this->attributes['system'] ?? null;
            },
            set: function (string $system): string {
                if (!array_key_exists($system, config('commlink.systems'))) {
                    throw new RuntimeException('Invalid system');
                }
                $this->attributes['system'] = $system;
                return $system;
            },
        );
    }

    public static function findForWebhook(
        string $guild_id,
        string $webhook_id,
    ): ?self {
        return self::discord()
            ->where('server_id', $guild_id)
            ->where(
                'webhook',
                'LIKE',
                sprintf('https://discord.com/api/webhooks/%s/%%', $webhook_id),
            )
            ->first();
    }
}
