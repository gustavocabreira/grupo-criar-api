<?php

namespace App\Services;

use App\Models\Product;

class PriceCalculator
{
    public function calculateFinalPrice(Product $product): float
    {
        $basePrice = $product->price;
        $discounts = $this->getApplicableDiscounts($product);

        foreach ($discounts as $discount) {
            $basePrice = $this->applyDiscount($basePrice, $discount);
        }

        return max(0, $basePrice);
    }

    private function getApplicableDiscounts(Product $product)
    {
        $campaigns = $product->relationLoaded('campaigns')
            ? $product->campaigns
            : $product->campaigns()->with('discounts')->get();

        return $campaigns->flatMap(fn ($campaign) => $campaign->discounts);
    }

    private function applyDiscount(float $price, $discount): float
    {
        if ($discount->percentage) {
            $price -= ($price * ($discount->percentage / 100));
        }

        if ($discount->value) {
            $price -= $discount->value;
        }

        return $price;
    }
}
