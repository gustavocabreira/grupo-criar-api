<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Services\PriceCalculator;

#[ObservedBy(ProductObserver::class)]
class Product extends Model
{
    use HasFactory;

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
        $priceCalculator = new PriceCalculator();
        return $priceCalculator->calculateFinalPrice($this);
    }
}
