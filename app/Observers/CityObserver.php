<?php

namespace App\Observers;

use App\Models\City;

class CityObserver
{
    public function creating(City $city): void
    {
        $city->is_active = true;
    }
}
