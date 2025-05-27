<?php

namespace App\Services;

use App\Models\AssignedEmail;
use App\Models\User;
use App\Notifications\EmailAssignedNotification;
use Illuminate\Support\Facades\Log;

class AssignmentService
{
    public function assignEmail(AssignedEmail $email, User $assignee, ?User $assigner = null): bool
    {
        try {
            $email->update([
                'assigned_to' => $assignee->id,
                'assigned_by' => $assigner?->id,
                'status' => 'assigned'
            ]);

            $assignee->notify(new EmailAssignedNotification($email));
            
            return true;
        } catch (\Exception $e) {
            Log::error("Assignment failed: " . $e->getMessage());
            return false;
        }
    }

    public function autoAssignEmail(AssignedEmail $email): bool
    {
        $availableStaff = User::where('is_available', true)
            ->where('is_active', true)
            ->withCount(['assignedEmails' => function($query) {
                $query->whereIn('status', ['assigned', 'in_progress']);
            }])
            ->get();

        if ($availableStaff->isEmpty()) {
            return false;
        }

        $bestCandidate = $availableStaff->sortBy('assigned_emails_count')->first();
        
        return $this->assignEmail($email, $bestCandidate);
    }
}