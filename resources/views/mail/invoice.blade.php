<!DOCTYPE html>
<html>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
<table width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;">
    <tr><td style="height:6px;background:#0ea5e9;"></td></tr>
    <tr><td style="padding:28px;">
        <h1 style="margin:0 0 4px;font-size:20px;color:#0b2545;">{{ config('pos.company.name') }}</h1>
        <p style="margin:0 0 20px;font-size:13px;color:#64748b;">Invoice {{ $order->invoice_number }} &middot; Order {{ $order->order_number }}</p>

        <p style="font-size:14px;">Hi {{ $order->customer->name }}, thanks for your order. Here is your invoice summary.</p>

        <table width="100%" cellpadding="6" cellspacing="0" style="font-size:13px;border-collapse:collapse;margin-top:12px;">
            <tr style="border-bottom:2px solid #2563eb;color:#64748b;text-align:left;">
                <th align="left">Item</th><th align="right">Qty</th><th align="right">Amount</th>
            </tr>
            @foreach ($order->items as $item)
                <tr style="border-bottom:1px solid #e2e8f0;">
                    <td>{{ $item->product_name }}</td>
                    <td align="right">{{ $item->quantity }}</td>
                    <td align="right">{{ money($item->line_total) }}</td>
                </tr>
            @endforeach
        </table>

        <table width="100%" cellpadding="4" cellspacing="0" style="font-size:13px;margin-top:12px;">
            <tr><td align="right" style="color:#64748b;">Taxable amount</td><td align="right" width="110">{{ money($order->taxable_amount) }}</td></tr>
            <tr><td align="right" style="color:#64748b;">Tax</td><td align="right">{{ money($order->tax_amount) }}</td></tr>
            <tr><td align="right" style="font-weight:bold;">Total due</td><td align="right" style="font-weight:bold;font-size:16px;color:#1d4ed8;">{{ money($order->grand_total) }}</td></tr>
        </table>

        <p style="margin-top:24px;font-size:12px;color:#94a3b8;">{{ config('pos.company.address') }} &middot; {{ config('pos.company.email') }}</p>
    </td></tr>
</table>
</body>
</html>
