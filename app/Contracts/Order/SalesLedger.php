<?php

namespace App\Contracts\Order;

use App\Models\JournalEntry;
use App\Models\Order;

interface SalesLedger
{
    public function postOrderCompletion(Order $order): JournalEntry;
}
