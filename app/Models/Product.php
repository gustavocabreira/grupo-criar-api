<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use Hasfactory;

    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'double',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'final_price',
    ];

    public function attachments(): BelongsToMany
    {
        return $this
            ->belongsToMany(Attachment::class, 'product_attachment_pivot')
            ->withTimestamps();
    }

    public function scopeFilterByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', '%' . $name . '%');
    }

    public function campaigns(): BelongsToMany
    {
        return $this
            ->belongsToMany(Campaign::class, 'campaign_product_pivot')
            ->withTimestamps();
    }

    public function getFinalPriceAttribute(): float
    {
        $campaigns = $this->relationLoaded('campaigns')
            ? $this->campaigns
            : $this->campaigns()->with('discounts')->get();

        if ($campaigns->isEmpty()) {
            return $this->price;
        }

        $discounts = $campaigns->flatMap(fn ($campaign) => $campaign->discounts);

        if ($discounts->isEmpty()) {
            return $this->price;
        }

        $price = $this->price;

        foreach ($discounts as $discount) {
            if ($discount->percentage) {
                $price -= ($price * ($discount->percentage / 100));
            }
        }

        foreach ($discounts as $discount) {
            if ($discount->value) {
                $price -= $discount->value;
            }
        }

        return max(0, $price);
    }


}
