<?php

namespace App\Observers;

use App\Models\Cluster;

class ClusterObserver
{
    public function creating(Cluster $cluster): void
    {
        $cluster->is_active = true;
    }
}
