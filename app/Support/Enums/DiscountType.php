<?php

namespace App\Support\Enums;

enum DiscountType: string
{
    case Percent = 'percent';
    case Fixed = 'fixed';
}
