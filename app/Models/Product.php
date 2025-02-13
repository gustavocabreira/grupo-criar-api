<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
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

    public function attachments(): BelongsToMany
    {
        return $this
            ->belongsToMany(Attachment::class, 'product_attachment_pivot')
            ->withTimestamps();
    }
}
