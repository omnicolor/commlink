<?php

declare(strict_types=1);

namespace App\Models;

use App\Mail\Auth\ForgotPassword;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Laravel\Pennant\Concerns\HasFeatures;
use Laravel\Pennant\Feature;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use stdClass;

/**
 * @property string $email
 * @property int $id
 * @property Collection $events
 * @property stdClass $pivot
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasFeatures;
    use HasRoles;
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
     * @psalm-suppress PossiblyUnusedMethod
     * @return BelongsToMany
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot('status');
    }

    /**
     * Get the campaigns the user has registered.
     * @psalm-suppress PossiblyUnusedMethod
     * @return HasMany
     */
    public function campaignsRegistered(): HasMany
    {
        return $this->hasMany(Campaign::class, 'registered_by', 'id');
    }

    /**
     * Get the campaigns the user is gamemastering.
     * @psalm-suppress PossiblyUnusedMethod
     * @return HasMany
     */
    public function campaignsGmed(): HasMany
    {
        return $this->hasMany(Campaign::class, 'gm', 'id');
    }

    /**
     * Get the user's channels.
     * @psalm-suppress PossiblyUnusedMethod
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
     * @psalm-suppress PossiblyUnusedMethod
     * @return HasMany
     */
    public function chatUsers(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Return all feature flags enabled for the user.
     * @return array<int, string>
     */
    public function getFeatures(): array
    {
        $features = Feature::for($this)->all();
        $features = array_filter($features, function (bool $feature): bool {
            return $feature;
        });
        $features = array_keys($features);
        array_walk($features, function (string &$value): void {
            $value = str_replace('App\\Features\\', '', $value);
        });
        sort($features);
        return $features;
    }

    public function sendPasswordResetNotification($token): void
    {
        Mail::to($this)->send(new ForgotPassword($token));
    }
}
