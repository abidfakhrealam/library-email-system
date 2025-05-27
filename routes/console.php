<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('email:stats', function () {
    $count = \App\Models\AssignedEmail::count();
    $unassigned = \App\Models\AssignedEmail::where('status', 'unassigned')->count();
    
    $this->info("Total emails: {$count}");
    $this->info("Unassigned emails: {$unassigned}");
})->purpose('Show email statistics');
