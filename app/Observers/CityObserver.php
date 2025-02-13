<?php

namespace App\Observers;

use App\Models\City;

class CityObserver
{
    public function creating(City $city): void
    {
        $city->is_active = true;
    }

    public function updated(City $city): void
    {
        if (!$city->is_active) {
            $city->clusters()->update(['cluster_city_pivot.is_active' => false]);
        }
    }
}
