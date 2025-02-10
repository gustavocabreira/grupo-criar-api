<?php

namespace App\Observers;

use App\Models\State;

class StateObserver
{
    public function creating(State $state)
    {
        $state->is_active = true;
    }
}
