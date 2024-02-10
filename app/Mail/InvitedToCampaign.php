<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\Campaign;
use App\Models\CampaignInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @codeCoverageIgnore
 */
class InvitedToCampaign extends Mailable
{
    use Queueable;
    use SerializesModels;

    protected Campaign $campaign;
    protected string $system;

    public function __construct(public CampaignInvitation $invitation)
    {
        /** @var Campaign */
        $campaign = $invitation->campaign;
        $this->campaign = $campaign;
        $this->system = $this->campaign->system;
        if (array_key_exists($this->campaign->system, config('app.systems'))) {
            $this->system = config('app.systems')[$this->campaign->system];
        }
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf(
                'Invitation to play %s (%s)',
                $this->campaign->name,
                $this->system,
            ),
        );
    }

    /**
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function content(): Content
    {
        $hash = $this->invitation->hash();
        return new Content(
            html: 'mail.InvitedToCampaign',
            with: [
                'accept_url' => route(
                    'campaign.invitation-accept',
                    [
                        'campaign' => $this->campaign,
                        'invitation' => $this->invitation->id,
                        'token' => $hash,
                    ],
                ),
                'decline_url' => route(
                    'campaign.invitation-decline',
                    [
                        'campaign' => $this->campaign,
                        'invitation' => $this->invitation->id,
                        'token' => $hash,
                    ],
                ),
                'campaign' => $this->campaign,
                'invitor' => $this->invitation->invitor,
                'name' => $this->invitation->name,
                'spam_url' => route(
                    'campaign.invitation-spam',
                    [
                        'campaign' => $this->campaign,
                        'invitation' => $this->invitation->id,
                        'token' => $hash,
                    ],
                ),
                'system' => $this->system,
            ],
        );
    }
}
