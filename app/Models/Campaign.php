<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Nwidart\Modules\Facades\Module;
use stdClass;

use function class_exists;
use function ucfirst;

/**
 * Class representing a gaming campaign or one-shot.
 * @method static int count()
 * @method static ?Campaign find(int $id)
 * @method static Builder where(string $field, mixed $value)
 * @mixin Model
 * @property string $description
 * @property-read ?User $gamemaster
 * @property ?int $gm
 * @property int $id
 * @property string $name
 * @property array<string, mixed> $options
 * @property int $registered_by
 * @property-read ?User $registrant
 * @property string $system
 * @property-read Collection<User> $users
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
     * @var list<string>
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
        return (string)$this->attributes['name'];
    }

    /**
     * Get a collection of channels attached to the campaign.
     */
    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    /**
     * Return characters playing in the campaign.
     * @return Collection<int, Character>
     */
    public function characters(): Collection
    {
        return Character::where('campaign_id', $this->id)->get();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the user that is GMing the campaign.
     */
    public function gamemaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gm', 'id');
    }

    /**
     * Return the initiatives rolled for the campaign.
     */
    public function initiatives(): HasMany
    {
        return $this->hasMany(Initiative::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CampaignInvitation::class);
    }

    /**
     * Create a new Campaign, subclassed if available.
     * @param array<int|string, mixed>|Campaign $attributes
     * @param ?string $connection
     */
    public function newFromBuilder(
        $attributes = [],
        $connection = null,
    ): static {
        if (
            $attributes instanceof Campaign
            || $attributes instanceof stdClass
        ) {
            $attributes = (array)$attributes;
        }
        $system = $attributes['system'];
        $class = 'Modules\\' . ucfirst($system) . '\\Models\\Campaign';
        if (
            null !== Module::find($system)
            && Module::isEnabled($system)
            && class_exists($class)
        ) {
            /** @var Campaign */
            $campaign = new $class($attributes);
        } else {
            $campaign = new Campaign($attributes);
        }

        $campaign->exists = true;
        $campaign->setRawAttributes($attributes, true);
        $campaign->setConnection($this->connection);
        $campaign->fireModelEvent('retrieved', false);
        // @phpstan-ignore return.type
        return $campaign;
    }

    /**
     * Get the user that registered the campaign.
     */
    public function registrant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Get a collection of users playing in the game (or at least invited).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status');
    }
}
