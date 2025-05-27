<?php

namespace App\Traits;

use App\Models\AssignedEmail;
use App\Models\User;

trait InteractsWithEmail
{
    public function assignTo(User $user, ?User $assigner = null): bool
    {
        return $this->update([
            'assigned_to' => $user->id,
            'assigned_by' => $assigner?->id,
            'status' => 'assigned'
        ]);
    }

    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function addTags(array $tagIds): void
    {
        $this->tags()->syncWithoutDetaching($tagIds);
    }

    public function getResponseTime(): ?int
    {
        if ($this->status !== 'completed') {
            return null;
        }

        return $this->received_at->diffInHours($this->updated_at);
    }
}
