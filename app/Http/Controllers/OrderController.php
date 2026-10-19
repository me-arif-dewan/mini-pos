<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\OrderDTO;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Responses\OrderFormResponse;
use App\Support\Enums\OrderStatus;
use App\Exceptions\InsufficientStockException;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Account;
use App\Models\Order;
use App\Services\OrderService;
use DomainException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function index(Request $request): View
    {
        $status = OrderStatus::tryFrom((string) $request->query('status'));
        $search = trim((string) $request->query('q'));

        $orders = $this->orderService->get($request->query());

        return view('orders.index', [
            'orders' => $orders,
            'status' => $status,
            'search' => $search,
        ]);
    }

    public function create(): OrderFormResponse
    {
        return new OrderFormResponse();
    }

    public function edit(Order $order): OrderFormResponse
    {
        if (! $order->isPending()) {
            abort(403, 'Only pending orders can be edited.');
        }

        $order->load('items');

        return new OrderFormResponse($order);
    }

    public function update(UpdateOrderRequest $request, Order $order): RedirectResponse
    {
        if (! $order->isPending()) {
            abort(403, 'Only pending orders can be edited.');
        }

        $order = $this->orderService->update($order, OrderDTO::fromRequest($request));

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order {$order->order_number} updated.");
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $order = $this->orderService->create(OrderDTO::fromRequest($request), $request->user());

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order {$order->order_number} saved as pending. Stock is reserved only when you complete it.");
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'items.product', 'journalEntry.lines.account']);

        return view('orders.show', compact('order'));
    }

    public function complete(Order $order): RedirectResponse
    {
        try {
            $order = $this->orderService->complete($order);
        } catch (InsufficientStockException $e) {
            return back()->with('stock_errors', $e->problems);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order completed. Stock deducted and {$order->invoice_number} posted to the ledger.");
    }

    public function cancel(Order $order): RedirectResponse
    {
        try {
            $this->orderService->cancel($order);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('orders.index')->with('success', "Order {$order->order_number} cancelled.");
    }

    public function invoice(Order $order): View
    {
        $order->load(['customer', 'user', 'items', 'journalEntry.lines.account']);

        $accounts = Account::whereIn('code', array_values(config('pos.accounts')))->get()->keyBy('code');

        // Completed orders show the real journal; pending ones show what will be posted.
        $lines = $order->journalEntry
            ? $order->journalEntry->lines->map(fn($line) => [
                'code' => $line->account->code,
                'name' => $line->account->name,
                'debit' => (float) $line->debit,
                'credit' => (float) $line->credit,
            ])
            : collect([
                ['code' => config('pos.accounts.receivable'), 'debit' => (float) $order->grand_total, 'credit' => 0.0],
                ['code' => config('pos.accounts.revenue'), 'debit' => 0.0, 'credit' => (float) $order->taxable_amount],
                ['code' => config('pos.accounts.tax_payable'), 'debit' => 0.0, 'credit' => (float) $order->tax_amount],
            ])->filter(fn($line) => $line['debit'] > 0 || $line['credit'] > 0)
            ->map(fn($line) => $line + ['name' => $accounts[$line['code']]->name]);

        return view('orders.invoice', [
            'order' => $order,
            'journalLines' => $lines->values(),
            'isProjected' => $order->journalEntry === null,
        ]);
    }
}
