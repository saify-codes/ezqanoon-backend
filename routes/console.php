<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:delete-lawyer-expired-tokens')->everyFiveMinutes();
