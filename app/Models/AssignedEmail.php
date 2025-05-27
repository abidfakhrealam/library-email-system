<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssignedEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'message_id',
        'subject',
        'body_preview',
        'from_email',
        'from_name',
        'received_at',
        'assigned_to',
        'assigned_by',
        'status',
        'notes',
        'priority',
        'auto_assigned'
    ];

    protected $casts = [
        'received_at' => 'datetime',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function markAsCompleted(): void
    {
        $this->update(['status' => 'completed']);
    }
}
