<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class representing a gaming campaign or one-shot.
 */
class Campaign extends Model
{
    use HasFactory;

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
     * Set the system for the campaign.
     * @param string $system
     * @throws \RuntimeException
     */
    public function setSystemAttribute(string $system): void
    {
        if (!array_key_exists($system, config('app.systems'))) {
            throw new \RuntimeException('Invalid system');
        }
        $this->attributes['system'] = $system;
    }
}
