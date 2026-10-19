<?php

namespace App\Services;

use App\Contracts\Order\SalesLedger;
use App\Support\Enums\OrderStatus;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\LedgerEntry;
use App\Models\Order;
use LogicException;

class AccountingService implements SalesLedger
{
    public function postOrderCompletion(Order $order): JournalEntry
    {
        $net = (float) $order->taxable_amount;
        $tax = (float) $order->tax_amount;
        $total = (float) $order->grand_total;

        if (abs($total - round($net + $tax, 2)) > 0.004) {
            throw new LogicException("Journal for {$order->order_number} is out of balance.");
        }

        $entry = JournalEntry::create([
            'reference' => 'JE-' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT),
            'entry_date' => now()->toDateString(),
            'description' => "Sales invoice {$order->invoice_number} - {$order->customer->name}",
            'order_id' => $order->id,
        ]);

        $lines = [
            [config('pos.accounts.receivable'), $total, 0.0],
            [config('pos.accounts.revenue'), 0.0, $net],
            [config('pos.accounts.tax_payable'), 0.0, $tax],
        ];

        foreach ($lines as [$code, $debit, $credit]) {
            if ($debit == 0.0 && $credit == 0.0) {
                continue;
            }

            $this->post($entry, $code, $debit, $credit);
        }

        return $entry;
    }

    private function post(JournalEntry $entry, string $accountCode, float $debit, float $credit): LedgerEntry
    {
        $account = Account::where('code', $accountCode)->lockForUpdate()->firstOrFail();

        $movement = $account->type->isDebitNormal() ? $debit - $credit : $credit - $debit;
        $balance = round((float) $account->balance + $movement, 2);

        $account->update(['balance' => $balance]);

        return $entry->lines()->create([
            'account_id' => $account->id,
            'debit' => $debit,
            'credit' => $credit,
            'balance_after' => $balance,
        ]);
    }

    public function dashboard(): array
    {
        $accounts = Account::orderBy('code')->get();
        $byCode = $accounts->keyBy('code');

        $totalDebits = (float) LedgerEntry::sum('debit');
        $totalCredits = (float) LedgerEntry::sum('credit');

        $days = collect(range(6, 0))->map(fn(int $ago) => now()->subDays($ago)->startOfDay());

        $sales = Order::where('status', OrderStatus::Completed)
            ->where('completed_at', '>=', $days->first())
            ->get(['completed_at', 'taxable_amount'])
            ->groupBy(fn(Order $order) => $order->completed_at->toDateString())
            ->map(fn($group) => (float) $group->sum('taxable_amount'));

        return [
            'accounts' => $accounts,
            'receivable' => (float) ($byCode[config('pos.accounts.receivable')]->balance ?? 0),
            'revenue' => (float) ($byCode[config('pos.accounts.revenue')]->balance ?? 0),
            'taxPayable' => (float) ($byCode[config('pos.accounts.tax_payable')]->balance ?? 0),
            'totalDebits' => $totalDebits,
            'totalCredits' => $totalCredits,
            'isBalanced' => abs($totalDebits - $totalCredits) < 0.005,
            'counts' => Order::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
            'pendingValue' => (float) Order::where('status', OrderStatus::Pending)->sum('grand_total'),
            'recentEntries' => JournalEntry::with(['lines.account', 'order'])->latest('id')->limit(5)->get(),
            'salesByDay' => $days->map(fn($day) => [
                'label' => $day->format('D'),
                'total' => $sales[$day->toDateString()] ?? 0.0,
            ]),
        ];
    }
}
