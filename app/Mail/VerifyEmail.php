<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $url;
    public $pin;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $pin, $url)
    {
        //
        $this->user = $user;
        $this->pin = $pin;

        // Generate signed verification URL (valid for 24 hours)
        $this->url = $url;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Verification',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.verifyEmail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        return $this->subject('Verify Your Email')
            ->view('mail.verifyEmail')
            ->with([
                'user' => $this->user,
                'pin'  => $this->pin,
                'verificationUrl' => $this->url
            ]);
    }
}
