<?php

namespace App\Actions\State;

use App\Models\State;
use Illuminate\Database\Eloquent\Model;

class CreateStateAction
{
    public function handle(array $payload): Model
    {
        return State::query()->create($payload);
    }
}
