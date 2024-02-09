<?php

declare(strict_types=1);

namespace App\Models;

use App\Events\CampaignInvitationCreated;
use App\Events\CampaignInvitationUpdated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignInvitation extends Model
{
    use HasFactory;

    public const INVITED = 'invited';
    public const RESPONDED = 'responded';
    public const SPAM = 'spam';

    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => CampaignInvitationCreated::class,
        'updated' => CampaignInvitationUpdated::class,
    ];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'campaign_id',
        'email',
        'invited_by',
        'name',
        'responded_at',
        'status',
        'updated_at',
    ];

    /**
     * @codeCoverageIgnore
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @codeCoverageIgnore
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function invitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    public function hash(): string
    {
        return sha1(
            (string)$this->campaign_id . $this->id . config('app.key')
        );
    }
}
