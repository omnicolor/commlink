<?php

declare(strict_types=1);

namespace App\Models\Slack;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @property string $channel
 * @property string $system
 * @property string $team
 * @property string $user
 */
class Channel extends Model
{
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
        'system',
        'team',
        'user',
    ];

    /**
     * Return the system the channel is registered to.
     * @param ?string $value
     * @return string
     */
    public function getSystemAttribute(?string $value): string
    {
        return $value ?? 'unregistered';
    }
}
