<?php

namespace App\Actions\Cluster;

use App\Http\Requests\Cluster\ShowClusterRequest;
use App\Models\Cluster;

class ShowClusterAction
{
    public function handle(Cluster $cluster, ShowClusterRequest $request): Cluster
    {
        $includes = $request->input('includes', []);

        if (!empty($includes)) {
            $cluster->load($includes);
        }

        return $cluster;
    }
}
