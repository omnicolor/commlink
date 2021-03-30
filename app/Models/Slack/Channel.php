<?php

declare(strict_types=1);

namespace App\Models\Slack;

use App\Models\Character;
use App\Models\SlackLink;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property string $channel
 * @property string $system
 * @property string $team
 */
class Channel extends Model
{
    use HasFactory;

    /**
     * Character for the user and channel.
     * @var ?Character
     */
    protected ?Character $character = null;

    /**
     * The database connection that should be used by the model.
     * @var string
     */
    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     * @var string[]
     */
    protected $fillable = [
        'channel',
        'channel_name',
        'system',
        'team',
        'team_name',
    ];

    /**
     * User ID attached to the current Slack user.
     * @var string
     */
    public string $user = 'Unknown';

    /**
     * Username currently attached to the Slack user.
     * @var string
     */
    protected string $username;

    /**
     * Return the character linked to this user and channel.
     * @return ?Character
     */
    public function character(): ?Character
    {
        if (!is_null($this->character)) {
            return $this->character;
        }
        $slackLink = SlackLink::where('slack_team', $this->team)
            ->where('slack_user', $this->user)
            ->first();
        if (is_null($slackLink)) {
            $this->character = null;
        } else {
            $this->character = $slackLink->character();
        }
        return $this->character;
    }

    /**
     * Return the system the channel is registered to.
     * @param ?string $value
     * @return string
     */
    public function getSystemAttribute(?string $value): string
    {
        return $value ?? 'unregistered';
    }

    /**
     * Return the username for the channel.
     * @return string
     */
    public function getUsernameAttribute(): string
    {
        if (is_null($this->character())) {
            if (!isset($this->username)) {
                return 'Unknown';
            }
            return $this->username;
        }
        if (isset($this->username)) {
            return sprintf(
                '%s (%s)',
                $this->character()->handle ?? $this->character()->name,
                $this->username
            );
        }
        return $this->character()->handle ?? $this->character()->name;
    }

    /**
     * Set the username for the channel.
     * @param string $username
     * @return Channel
     */
    public function setUsernameAttribute(string $username): Channel
    {
        $this->username = $username;
        return $this;
    }
}
