<?php

namespace App\Mail;

use App\Models\QuoteRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;



class QuoteReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public QuoteRequest $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New Quote Request [{$this->quote->reference}] — {$this->quote->full_name}",
            to: config('sales.notification_email'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.quote-received',
        );
    }
}