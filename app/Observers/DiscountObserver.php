<?php

namespace App\Observers;

use App\Models\Discount;

class DiscountObserver
{
    public function creating(Discount $discount): void
    {
        $discount->is_active = true;
    }
}
