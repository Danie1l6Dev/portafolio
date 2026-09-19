<?php

namespace App\Mail;

use App\Models\Message;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class NewContactMessage extends Mailable
{
    public function __construct(public readonly Message $contactMessage) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->contactMessage->email, $this->contactMessage->name)],
            subject: 'Nuevo mensaje de contacto: '.$this->contactMessage->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.new-contact-message-html',
            text: 'emails.new-contact-message',
        );
    }
}
