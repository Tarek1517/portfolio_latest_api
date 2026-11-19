<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $recipientName;
    public string $replyMessage;
    public string $originalSubject;

    /**
     * @param string $recipientName   Contact's full name
     * @param string $replyMessage    Admin reply body
     * @param string $originalSubject Original contact subject
     */
    public function __construct(string $recipientName, string $replyMessage, string $originalSubject)
    {
        $this->recipientName   = $recipientName;
        $this->replyMessage    = $replyMessage;
        $this->originalSubject = $originalSubject;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Re: ' . $this->originalSubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reply',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
