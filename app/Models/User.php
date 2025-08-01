<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\AsEmail;
use App\Mail\Auth\ForgotPassword;
use App\ValueObjects\Email;
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
use Override;
use Spatie\Permission\Traits\HasRoles;
use Stringable;
use stdClass;

use function array_filter;
use function array_keys;
use function array_walk;
use function sort;
use function str_replace;

/**
 * @method static int count()
 * @property iterable<array-key, Campaign> $campaignsGmed
 * @property iterable<array-key, Campaign> $campaignsRegistered
 * @property Email $email
 * @property Collection $events
 * @property int $id
 * @property string $name
 * @property stdClass $pivot
 */
class User extends Authenticatable implements Stringable
{
    use HasApiTokens;
    use HasFactory;
    use HasFeatures;
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'name',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     * @var list<string>
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
        'email' => AsEmail::class,
        'email_verified_at' => 'datetime',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? 'Unnamed User';
    }

    /**
     * Get the campaigns for the user.
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class)
            ->withPivot('status');
    }

    /**
     * Get the campaigns the user has registered.
     */
    public function campaignsRegistered(): HasMany
    {
        return $this->hasMany(Campaign::class, 'registered_by', 'id');
    }

    /**
     * Get the campaigns the user is gamemastering.
     */
    public function campaignsGmed(): HasMany
    {
        return $this->hasMany(Campaign::class, 'gm', 'id');
    }

    /**
     * Get the user's channels.
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class, 'registered_by', 'id');
    }

    /**
     * Get the characters for the User.
     */
    public function characters(?string $system = null): Builder
    {
        $characters = Character::where('owner', $this->email->address);
        if (null !== $system) {
            $characters->where('system', $system);
        }
        return $characters;
    }

    /**
     * Get the user's chat server links.
     */
    public function chatUsers(): HasMany
    {
        return $this->hasMany(ChatUser::class);
    }

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
        $features = array_filter($features, static function (bool $feature): bool {
            return $feature;
        });
        $features = array_keys($features);
        array_walk($features, static function (string &$value): void {
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
