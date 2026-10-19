<?php

if (! function_exists('money')) {

    function money(float|int|string|null $amount): string
    {
        return config('pos.currency_symbol') . number_format((float) $amount, 2);
    }
}
