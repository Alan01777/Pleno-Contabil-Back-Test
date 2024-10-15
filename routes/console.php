<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\UserPushToken;
use Illuminate\Support\Facades\Http;

Schedule::exec('php artisan send:notifications')->everyTenSeconds();
