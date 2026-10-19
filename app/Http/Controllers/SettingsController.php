<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function show(): View
    {
        return view('settings', [
            'tab'      => request()->query('tab', 'pos'),
            'settings' => Setting::all()->pluck('value', 'key'),
        ]);
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $tab = $request->query('tab', 'pos');

        $map = match ($tab) {
            'company' => [
                'company.name'    => $request->company_name,
                'company.address' => $request->company_address,
                'company.email'   => $request->company_email,
                'company.phone'   => $request->company_phone,
            ],
            'identifier' => [
                'pos.identifier' => $request->identifier,
            ],
            default => [
                'pos.tax_rate'        => $request->tax_rate,
                'pos.currency_symbol' => $request->currency,
            ],
        };

        foreach ($map as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Setting::flush();

        return redirect()
            ->route('settings.show', ['tab' => $tab])
            ->with('success', 'Settings saved.');
    }
}
