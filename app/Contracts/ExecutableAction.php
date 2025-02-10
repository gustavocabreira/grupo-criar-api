<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;

interface ExecutableAction
{
    public function execute(array $payload): Model;
}
