<?php
use Illuminate\Support\Facades\Artisan;


Artisan::command('send:notifications')->everyMinute();
