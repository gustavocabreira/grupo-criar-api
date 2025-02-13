<?php

namespace App\Actions\Cluster\City;

use App\Http\Requests\Cluster\City\AssignCityRequest;
use App\Models\Cluster;
use Illuminate\Support\Facades\DB;

class SyncCitiesAction
{
    public function handle(Cluster $cluster, AssignCityRequest $request): Cluster
    {
        $cities = array_unique($request->input('cities'));

        DB::transaction(function () use ($cluster, $cities) {
            DB::table('cluster_city_pivot')
                ->whereIn('city_id', $cities)
                ->where('cluster_id', '!=', $cluster->id)
                ->update(['is_active' => false]);

            $cluster->cities()->whereNotIn('city_id', $cities)->update(['cluster_city_pivot.is_active' => false]);
            $cluster->cities()->syncWithoutDetaching($cities);
            $cluster->cities()->whereIn('city_id', $cities)->update(['cluster_city_pivot.is_active' => true]);
        });

        return $cluster;
    }
}
