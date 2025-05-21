<?php

declare(strict_types=1);

namespace App\Models;

use App\Casts\AsEmail;
use App\Events\CampaignInvitationCreated;
use App\Events\CampaignInvitationUpdated;
use App\ValueObjects\Email;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use function sha1;

/**
 * @property Campaign $campaign
 * @property int $campaign_id
 * @property Email $email
 * @property int $id
 * @property int $invited_by
 * @property-read User $invitor
 * @property string $name
 * @property string $responded_at
 * @property string $status
 * @property string $updated_at
 */
class CampaignInvitation extends Model
{
    use HasFactory;

    public const string INVITED = 'invited';
    public const string RESPONDED = 'responded';
    public const string SPAM = 'spam';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'campaign_id' => 'int',
        'email' => AsEmail::class,
    ];

    /**
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => CampaignInvitationCreated::class,
        'updated' => CampaignInvitationUpdated::class,
    ];

    /**
     * @var list<string>
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
     * @codeCoverageIngore
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    /**
     * @codeCoverageIngore
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
