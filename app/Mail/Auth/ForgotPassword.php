<?php

declare(strict_types=1);

namespace App\Mail\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use function route;

/**
 * @codeCoverageIgnore
 */
class ForgotPassword extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly string $token)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password reset',
        );
    }

    /**
     * Get the message content definition.
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
