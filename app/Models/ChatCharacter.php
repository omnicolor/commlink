<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatCharacter extends Model
{
    use HasFactory;

    /**
     * Mass assignable properties.
     * @var array<int, string>
     */
    protected $fillable = [
        'channel_id',
        'character_id',
        'chat_user_id',
    ];

    /**
     * Return the channel.
     * @return BelongsTo
     */
    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    /**
     * Return the chat user (Slack or Discord user).
     * @return BelongsTo
     */
    public function chatUser(): BelongsTo
    {
        return $this->belongsTo(ChatUser::class);
    }

    /**
     * Return the character.
     * @return ?Character
     */
    public function getCharacter(): ?Character
    {
        return Character::find($this->character_id);
    }
}
