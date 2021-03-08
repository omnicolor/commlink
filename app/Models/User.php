<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var string[]
     */
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the characters for the User.
     * @param ?string $system
     * @return Builder
     */
    public function characters(?string $system = null): Builder
    {
        $characters = Character::where('owner', $this->email);
        if (null !== $system) {
            $characters->where('type', $system);
        }
        return $characters;
    }

    /**
     * Get the user's Slack Links.
     * @return HasMany
     */
    public function slackLinks(): HasMany
    {
        return $this->hasMany(SlackLink::class);
    }
}
