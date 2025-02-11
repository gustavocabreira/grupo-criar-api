<?php

namespace App\Observers;

use App\Models\Campaign;

class CampaignObserver
{
    public function creating(Campaign $campaign): void
    {
        $campaign->is_active = true;
    }
}
