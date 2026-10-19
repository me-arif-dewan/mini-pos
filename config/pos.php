<?php

return [
    'tax_rate' => (float) env('POS_TAX_RATE', 5),

    'currency_symbol' => env('POS_CURRENCY', '$'),

    'company' => [
        'name' => env('POS_COMPANY_NAME', 'Northwind Trading Co.'),
        'address' => env('POS_COMPANY_ADDRESS', '12 Market Street, Dhaka 1212'),
        'email' => env('POS_COMPANY_EMAIL', 'accounts@northwind.test'),
        'phone' => env('POS_COMPANY_PHONE', '+880 1700 000000'),
    ],

    'accounts' => [
        'receivable' => '1100',
        'revenue' => '4000',
        'tax_payable' => '2100',
    ],

    'identifier' => env('POS_IDENTIFIER', 'id'),
];
