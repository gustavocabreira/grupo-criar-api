<?php

namespace App\Models;

use App\Observers\DiscountObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(DiscountObserver::class)]
class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';

    protected $fillable = [
        'name',
        'description',
        'value',
        'percentage',
        'is_active',
    ];

    protected $casts = [
        'value' => 'double',
        'percentage' => 'double',
        'is_active' => 'boolean',
    ];

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_discount_pivot')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function activeCampaigns(): BelongsToMany
    {
        return $this->campaigns()->wherePivot('is_active', true);
    }
}
