<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class representing a gaming campaign or one-shot.
 * @property string $description
 * @property int $id
 * @property string $name
 * @property array<string, mixed> $options
 * @property string $system
 */
class Campaign extends Model
{
    use GameSystem;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'gm' => 'int',
        'options' => 'array',
        'registered_by' => 'int',
    ];

    /**
     * Attributes that are mass assignable.
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'gm',
        'name',
        'options',
        'registered_by',
        'system',
    ];

    public function __toString(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Get a collection of channels attached to the campaign.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * Return characters playing in the campaign.
     */
    public function characters(): Collection
    {
        return Character::where('campaign_id', $this->id)->get();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the user that is GMing the campaign.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function gamemaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gm', 'id');
    }

    /**
     * Return the initiatives rolled for the campaign.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function invitations(): HasMany
    {
        return $this->hasMany(CampaignInvitation::class);
    }

    /**
     * Create a new Campaign, subclassed if available.
     * @phpstan-ignore-next-line
     * @param array<mixed, mixed> $attributes
     * @param ?string $connection
     * @psalm-suppress InvalidPropertyFetch
     * @return static
     */
    public function newFromBuilder(
        $attributes = [],
        $connection = null
    ): static {
        // @phpstan-ignore-next-line
        switch ($attributes->system ?? null) {
            case 'shadowrun5e':
                $campaign = new Shadowrun5e\Campaign((array)$attributes);
                break;
            default:
                $campaign = new Campaign((array)$attributes);
                break;
        }

        $campaign->exists = true;
        $campaign->setRawAttributes((array)$attributes, true);
        $campaign->setConnection($this->connection);
        $campaign->fireModelEvent('retrieved', false);
        // @phpstan-ignore-next-line
        return $campaign;
    }

    /**
     * Get the user that registered the campaign.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get a collection of users playing in the game (or at least invited).
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status');
    }
}
