<?php

namespace App\Observers;

use App\Models\State;

class StateObserver
{
    public function creating(State $state)
    {
        $state->is_active = true;
    }

    public function updated(State $state)
    {
        if (!$state->is_active) {
            $state->cities()->update(['is_active' => false]);
        }
    }
}
