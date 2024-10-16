<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\UserPushToken;
use Illuminate\Support\Facades\Http;

// Schedule the command with different parameters on different days
Schedule::command('send:notifications DAS')->monthlyOn(18, '12:00');
Schedule::command('send:notifications PARCELAMENTO')->monthlyOn(2, '1:00');
Schedule::command('send:notifications PIS')->monthlyOn(23, '12:00');
Schedule::command('send:notifications COFINS')->monthlyOn(23, '12:00');
Schedule::command('send:notifications ICMS')->monthlyOn(27, '12:00');
Schedule::command('send:notifications FOLHAS')->monthlyOn(28, '12:00');
Schedule::command('send:notifications FGTS')->monthlyOn(17, '12:00');