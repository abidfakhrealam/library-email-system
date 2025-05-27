<?php

namespace App\Jobs;

use App\Models\AssignedEmail;
use App\Services\AutomationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessIncomingEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public AssignedEmail $email
    ) {}

    public function handle(AutomationService $automationService)
    {
        // Apply automation rules
        $automationService->processNewEmail($this->email);

        // If still unassigned after automation, try to auto-assign
        if ($this->email->status === 'unassigned') {
            $automationService->autoAssignEmail($this->email);
        }
    }
}
