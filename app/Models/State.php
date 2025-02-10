<?php

namespace App\Models;

use App\Observers\StateObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy(StateObserver::class)]
class State extends Model
{
    use HasFactory;

    protected $table = 'states';

    protected $fillable = [
        'acronym',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
