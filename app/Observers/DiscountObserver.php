<?php

namespace App\Observers;

use App\Models\Discount;

class DiscountObserver
{
    public function creating(Discount $discount): void
    {
        $discount->is_active = true;
        $discount->value = $discount->value ?? 0;
        $discount->percentage = $discount->percentage ?? 0;
    }

    public function updating(Discount $discount): void
    {
        $discount->value = $discount->value ?? 0;
        $discount->percentage = $discount->percentage ?? 0;
    }
}
