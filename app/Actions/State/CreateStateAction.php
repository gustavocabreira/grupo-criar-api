<?php

namespace App\Actions\State;

use App\Contracts\ExecutableAction;
use App\Models\State;
use Illuminate\Database\Eloquent\Model;

class CreateStateAction implements ExecutableAction
{
    public function execute(array $payload): Model
    {
        return State::query()->create($payload);
    }
}
