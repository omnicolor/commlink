<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Link from a user to a Slack team and user.
 */
class SlackLink extends Model
{
    use HasFactory;

    /**
     * Attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'character_id',
        'slack_team',
        'slack_user',
        'user_id',
    ];

    /**
     * Return the character linked to the channel.
     * @return Character
     */
    public function character(): ?Character
    {
        return Character::where('_id', $this->character_id)->first();
    }

    /**
     * Return the user the link belongs to.
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
