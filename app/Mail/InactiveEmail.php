<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InactiveEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;
    public $days;
    public $domain;

    /**
     * Create a new message instance.
     */
    public function __construct($userName, $days, $domain)
    {
        $this->userName = $userName;
        $this->days = $days;
        $this->domain = $domain;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thông báo: Bạn đã ' . $this->days . ' ngày chưa học bài mới!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.inactive',
            with: [
                'userName' => $this->userName,
                'days' => $this->days,
                'domain' => $this->domain,
            ],
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
}
