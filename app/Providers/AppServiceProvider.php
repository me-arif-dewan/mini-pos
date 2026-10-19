<?php

namespace App\Providers;

use App\Contracts\Order\OrderTotalsCalculator;
use App\Contracts\Order\SalesLedger;
use App\Services\AccountingService;
use App\Services\OrderCalculator;
use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(OrderTotalsCalculator::class, OrderCalculator::class);
        $this->app->bind(SalesLedger::class, AccountingService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        config([
            'pos.tax_rate'        => Setting::get('pos.tax_rate', '5'),
            'pos.currency_symbol' => Setting::get('pos.currency_symbol', '$'),
            'pos.identifier'      => Setting::get('pos.identifier', 'uuid'),
            'pos.company.name'    => Setting::get('company.name', 'Mini POS'),
            'pos.company.address' => Setting::get('company.address', ''),
            'pos.company.email'   => Setting::get('company.email', ''),
            'pos.company.phone'   => Setting::get('company.phone', ''),
        ]);
    }
}
