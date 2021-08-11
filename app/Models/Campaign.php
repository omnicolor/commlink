<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Traits\GameSystem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * The attributes that should be cast.
     * @var array<string, string>
     */
    protected $casts = [
        'options' => 'array',
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

    /**
     * Return the campaign's name.
     * @return string
     */
    public function __toString(): string
    {
        return $this->attributes['name'];
    }

    /**
     * Get a collection of users playing in the game (or at least invited).
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('status');
    }

    /**
     * Get the user that is GMing the campaign.
     * @return BelongsTo
     */
    public function gamemaster(): BelongsTo
    {
        return $this->belongsTo(User::class, 'gm', 'id');
    }

    /**
     * Get the user that registered the campaign.
     * @return BelongsTo
     */
    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    /**
     * Return characters playing in the campaign.
     * @return Collection
     */
    public function characters(): Collection
    {
        return Character::where('campaign_id', $this->id)->get();
    }

    /**
     * Set the system for the campaign.
     * @param string $system
     * @throws \RuntimeException
     */
    public function setSystemAttribute(string $system): void
    {
        if (!\array_key_exists($system, config('app.systems'))) {
            throw new \RuntimeException('Invalid system');
        }
        $this->attributes['system'] = $system;
    }
}
