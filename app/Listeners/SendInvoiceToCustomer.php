<?php

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Mail\InvoiceIssued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendInvoiceToCustomer implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public array $backoff = [30, 120];

    public function handle(OrderCompleted $event): void
    {
        $order = $event->order->loadMissing(['customer', 'items']);

        if (! $order->customer->email) {
            return;
        }

        Mail::to($order->customer->email, $order->customer->name)->send(new InvoiceIssued($order));
    }
}
