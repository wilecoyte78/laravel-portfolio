<?php

// app/Mail/ExceptionOccurred.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ExceptionOccurredMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: sprintf('[%s] %s: %s',
                strtoupper($this->data['env']),
                class_basename($this->data['class']),
                \Illuminate\Support\Str::limit($this->data['message'], 80),
            ),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'emails.exception');
    }
}