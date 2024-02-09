<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * @codeCoverageIgnore
 */
class ForgotPassword extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public string $token)
    {
    }

    /**
     * Get the message envelope.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password reset',
        );
    }

    /**
     * Get the message content definition.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function content(): Content
    {
        return new Content(
            html: 'mail.auth.forgot-password',
            with: [
                'url' => route('password.reset', ['token' => $this->token]),
            ],
        );
    }
}
