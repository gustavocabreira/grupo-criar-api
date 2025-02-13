<?php

namespace App\Actions\City;

use App\Http\Requests\City\IndexCityRequest;
use App\Models\City;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexCityAction
{
    public function handle(IndexCityRequest $request): LengthAwarePaginator
    {
        $cities = City::query();

        if ($request->has('includes')) {
            $cities->with($request->input('includes'));
        }

        return $cities->paginate($request->input('perPage') ?? 10);
    }
}
