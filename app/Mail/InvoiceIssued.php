<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class InvoiceIssued extends Mailable
{
    public function __construct(public readonly Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Invoice {$this->order->invoice_number} from ".config('pos.company.name'),
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.invoice');
    }
}
