<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Class representing a real life event, like a game session.
 * @property Campaign $campaign
 * @property int $campaign_id
 * @property int $created_by
 * @property User $creator
 * @property ?string $description
 * @property null|string|Carbon $game_end
 * @property null|string|Carbon $game_start
 * @property string $name
 * @property null|string|Carbon $real_end
 * @property string|Carbon $real_start
 * @property Collection $responses
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
        //'created' => CampaignEventCreated::class,
        //'deleted' => CampaignEventDeleted::class,
        //'updated' => CampaignEventUpdated::class,
    ];

    /**
     * @var array<int, string>
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

    public function __toString(): string
    {
        if (null !== $this->name) {
            return $this->name;
        }

        return (new CarbonImmutable($this->real_start))->toDayDateTimeString();
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function responses(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }
}
