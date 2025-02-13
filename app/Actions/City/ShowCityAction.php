<?php

namespace App\Actions\City;

use App\Http\Requests\City\ShowCityRequest;
use App\Models\City;

class ShowCityAction
{
    public function handle(City $city, ShowCityRequest $request): City
    {
        $includes = $request->input('includes', []);

        if (!empty($includes)) {
            $city->load($includes);
        }

        return $city;
    }
}
