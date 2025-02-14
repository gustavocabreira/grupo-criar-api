<?php

namespace App\Models;

use App\Observers\CampaignObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(CampaignObserver::class)]
class Campaign extends Model
{
    use HasFactory;

    protected $table = 'campaigns';

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $with = [
        'products',
    ];

    public function clusters(): BelongsToMany
    {
        return $this
            ->belongsToMany(Cluster::class, 'cluster_campaign_pivot')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function activeClusters(): BelongsToMany
    {
        return $this->clusters()->wherePivot('is_active', true);
    }

    public function discounts(): BelongsToMany
    {
        return $this
            ->belongsToMany(Discount::class, 'campaign_discount_pivot')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function activeDiscounts(): BelongsToMany
    {
        return $this->discounts()->wherePivot('is_active', true);
    }

    public function products(): BelongsToMany
    {
        return $this
            ->belongsToMany(Product::class, 'campaign_product_pivot')
            ->withTimestamps();
    }
}
