<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read Channel $channel
 * @property int $channel_id
 * @property string $character_id
 * @property-read ChatUser $chat_user
 * @property int $chat_user_id
 */
class ChatCharacter extends Model
{
    use HasFactory;

    /**
     * Mass assignable properties.
     * @var list<string>
     */
    protected $fillable = [
        'channel_id',
        'character_id',
        'chat_user_id',
    ];

    /**
     * Return the channel.
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Return the chat user (Slack or Discord user).
     */
    public function chatUser(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class);
    }

    /**
     * Return the character.
     */
    public function getCharacter(): ?Character
    {
        return Character::find($this->character_id);
    }
}
