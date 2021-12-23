<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property string $email
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var array<int, string>
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
     * Get the campaigns for the user.
     * @return BelongsToMany
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot('status');
    }

    /**
     * Get the campaigns the user has registered.
     * @return HasMany
     */
    public function campaignsRegistered(): HasMany
    {
        return $this->hasMany(Campaign::class, 'registered_by', 'id');
    }

    /**
     * Get the campaigns the user is gamemastering.
     * @return HasMany
     */
    public function campaignsGmed(): HasMany
    {
        return $this->hasMany(Campaign::class, 'gm', 'id');
    }

    /**
     * Get the user's channels.
     * @return HasMany
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'registered_by', 'id');
    }

    /**
     * Get the characters for the User.
     * @param ?string $system
     * @return Builder
     */
    public function characters(?string $system = null): Builder
    {
        $characters = Character::where('owner', $this->email);
        if (null !== $system) {
            $characters->where('system', $system);
        }
        return $characters;
    }

    /**
     * Get the user's chat server links.
     * @return HasMany
     */
    public function chatUsers(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }
}
