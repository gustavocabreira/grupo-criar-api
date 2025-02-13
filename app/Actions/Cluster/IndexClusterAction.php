<?php

namespace App\Actions\Cluster;

use App\Http\Requests\Cluster\IndexClusterRequest;
use App\Models\Cluster;
use Illuminate\Pagination\LengthAwarePaginator;

class IndexClusterAction
{
    public function handle(IndexClusterRequest $request): LengthAwarePaginator
    {
        $cluster = Cluster::query();


        return $cluster->paginate($request->input('perPage') ?? 10);

    }
}
