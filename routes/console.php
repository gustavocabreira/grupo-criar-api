<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('attachment:prune')->daily();
