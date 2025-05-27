<?php

return [
    'subject' => 'Subject',
    'from' => 'From',
    'to' => 'To',
    'date' => 'Date',
    'status' => 'Status',
    'assigned_to' => 'Assigned To',
    'actions' => 'Actions',
    'no_emails' => 'No emails found',
    'reply_sent' => 'Reply sent successfully',
    'reply_failed' => 'Failed to send reply',
    'assignment_success' => 'Email assigned successfully',
    'assignment_failed' => 'Failed to assign email',
    'status_updated' => 'Status updated successfully',
    'invalid_status' => 'Invalid status',
    
    'statuses' => [
        'unassigned' => 'Unassigned',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'completed' => 'Completed'
    ],
    
    'priorities' => [
        'low' => 'Low',
        'medium' => 'Medium',
        'high' => 'High'
    ],
    
    'notifications' => [
        'assigned' => 'You have been assigned a new email: :subject',
        'completed' => 'Email marked as completed: :subject'
    ]
];
