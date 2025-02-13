<?php

namespace App\Observers;

use App\Models\Campaign;

class CampaignObserver
{
    public function creating(Campaign $campaign): void
    {
        $campaign->is_active = true;
    }

    public function updated(Campaign $campaign): void
    {
        if (!$campaign->is_active) {
            $campaign->activeClusters()->update(['cluster_campaign_pivot.is_active' => false]);
            $campaign->activeDiscounts()->update(['campaign_discount_pivot.is_active' => false]);
        }
    }
}
