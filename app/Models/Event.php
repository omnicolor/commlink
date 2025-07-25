<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\EventCreated;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Override;

use function now;

/**
 * Class representing a real life event, like a game session.
 * @method static ?self Find(string $id)
 * @method self firstOrFail()
 * @property Campaign $campaign
 * @property int $campaign_id
 * @property Carbon|string $created_at
 * @property int $created_by
 * @property User $creator
 * @property ?string $description
 * @property Carbon|null $game_end
 * @property Carbon|null $game_start
 * @property-read int $id
 * @property string $name
 * @property Carbon|null $real_end
 * @property Carbon $real_start
 * @property Collection $responses
 * @property Carbon|string $updated_at
 */
class Event extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'real_end' => 'datetime',
        'real_start' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => EventCreated::class,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'campaign_id',
        'created_by',
        'description',
        'game_end',
        'game_start',
        'name',
        'real_end',
        'real_start',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name ?? (new CarbonImmutable($this->real_start))->toDayDateTimeString();
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function scopeForCampaign(Builder $query, Campaign $campaign): void
    {
        $query->where('campaign_id', $campaign->id);
    }

    public function scopeFuture(Builder $query): void
    {
        $query->where('real_start', '>=', now());
    }
}
