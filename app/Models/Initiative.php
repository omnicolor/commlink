<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Override;
use Stringable;

/**
 * @property ?int $campaign_id
 * @property ?int $channel_id
 * @property ?string $character_id
 * @property ?string $character_name
 * @property Carbon|string $created_at
 * @property ?int $grunt_id
 * @property int $initiative
 * @property-read string $name
 * @property Carbon|string $updated_at
 */
class Initiative extends Model implements Stringable
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * @var list<string>
     */
    protected $fillable = [
        // Either campaign_id or channel_id must be filled.
        'campaign_id',
        'channel_id',
        // Either character_id or name must be filled.
        'character_id',
        'character_name',
        'grunt_id',
        'initiative',
    ];

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'initiative' => 'int',
    ];

    #[Override]
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * Return the campaign attached to the initiative.
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * Return the name of the person with initiative.
     *
     * If the initiative belongs to a character, returns their name/handle. If
     * it's a mook, returns the name assigned to them when setting initiative.
     */
    public function name(): Attribute
    {
        return Attribute::make(
            get: function (): string {
                if (isset($this->attributes['character_name'])) {
                    return $this->attributes['character_name'];
                }
                $character = Character::find($this->attributes['character_id']);
                return (string)$character;
            },
        );
    }

    /**
     * Just return initiative rows for a given campaign.
     */
    public function scopeForCampaign(Builder $query, Campaign $campaign): Builder
    {
        return $query->where('campaign_id', $campaign->id);
    }

    /**
     * Return initiative rows for a given channel.
     */
    public function scopeForChannel(Builder $query, Channel $channel): Builder
    {
        return $query->where('channel_id', $channel->id);
    }
}
