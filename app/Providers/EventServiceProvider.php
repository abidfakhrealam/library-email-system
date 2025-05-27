<?php

namespace App\Providers;

use App\Events\EmailReplied;
use App\Events\NewEmailAssigned;
use App\Listeners\LogEmailAssignment;
use App\Listeners\LogEmailReply;
use App\Listeners\SendAssignmentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        NewEmailAssigned::class => [
            SendAssignmentNotification::class,
            LogEmailAssignment::class,
        ],
        EmailReplied::class => [
            LogEmailReply::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
