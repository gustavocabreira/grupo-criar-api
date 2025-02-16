<?php

namespace App\Models;

use App\Observers\ClusterObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy(ClusterObserver::class)]
class Cluster extends Model
{
    use HasFactory;

    protected $table = 'clusters';

    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities(): BelongsToMany
    {
        return $this->belongsToMany(City::class, 'cluster_city_pivot')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function activeCities(): BelongsToMany
    {
        return $this->cities()->wherePivot('is_active', true);
    }

    public function scopeFilterByName(Builder $query, string $name): Builder
    {
        return $query->where('name', 'LIKE', '%' . $name . '%');
    }
}
