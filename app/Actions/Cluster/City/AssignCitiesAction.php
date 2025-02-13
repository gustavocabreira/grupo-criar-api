<?php

namespace App\Actions\Cluster\City;

use App\Http\Requests\Cluster\City\AssignCityRequest;
use App\Models\Cluster;
use Illuminate\Support\Facades\DB;

class AssignCitiesAction
{
    public function handle(Cluster $cluster, AssignCityRequest $request): Cluster
    {
        DB::transaction(function () use ($cluster, $request) {
            DB::table('cluster_city_pivot')
                ->whereIn('city_id', $request->input('cities'))
                ->where('cluster_id', '!=', $cluster->id)
                ->update(['is_active' => false]);

            $cluster->cities()->attach(array_unique($request->input('cities')), ['is_active' => true]);
            $cluster->load('cities');
        });

        return $cluster;
    }
}
