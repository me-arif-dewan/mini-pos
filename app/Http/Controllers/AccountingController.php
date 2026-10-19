<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountingService;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function __construct(private readonly AccountingService $accounting) {}

    public function dashboard(): View
    {
        return view('accounting.dashboard', $this->accounting->dashboard());
    }

    public function ledger(Account $account): View
    {
        $entries = $account->ledgerEntries()
            ->with('journalEntry.order')
            ->orderBy('id')
            ->paginate(20);

        return view('accounting.ledger', compact('account', 'entries'));
    }
}
