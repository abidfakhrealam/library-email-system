<?php

namespace App\Http\Controllers

use App\Models\AssignedEmail;
use App\Models\User;
use App\Services\Email\GraphApiService;
use App\Services\AssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailController extends Controller
{
    public function __construct(
        private GraphApiService $emailService,
        private AssignmentService $assignmentService
    ) {}

    public function index(Request $request)
    {
        $sharedMailboxes = [
            'library-help@university.edu',
            'library-requests@university.edu',
            'library-support@university.edu'
        ];

        // Fetch new emails
        foreach ($sharedMailboxes as $mailbox) {
            $this->emailService->fetchEmails($mailbox);
        }

        // Get emails with filters
        $emails = AssignedEmail::query()
            ->with(['assignee', 'tags'])
            ->when($request->has('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->has('search'), fn($q) => $q->whereFullText(['subject', 'body_preview'], $request->search))
            ->orderBy('received_at', 'desc')
            ->paginate(25);

        $staff = User::where('is_staff', true)->get();
        $tags = Tag::all();

        return view('emails.index', compact('emails', 'staff', 'tags'));
    }

    public function assign(Request $request, AssignedEmail $email)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        $assignee = User::find($request->assigned_to);
        $success = $this->assignmentService->assignEmail(
            $email, 
            $assignee, 
            Auth::user()
        );

        if ($success) {
            return back()->with('success', 'Email assigned successfully');
        }

        return back()->with('error', 'Failed to assign email');
    }

    public function updateStatus(AssignedEmail $email, string $status)
    {
        $validStatuses = ['assigned', 'in_progress', 'completed'];
        
        if (!in_array($status, $validStatuses)) {
            return back()->with('error', 'Invalid status');
        }

        $email->update(['status' => $status]);

        return back()->with('success', 'Status updated successfully');
    }

    public function reply(Request $request, AssignedEmail $email)
    {
        $request->validate([
            'reply_content' => 'required|string'
        ]);

        $success = $this->emailService->sendReply($email->message_id, $request->reply_content);

        if ($success) {
            $email->markAsCompleted();
            return back()->with('success', 'Reply sent successfully');
        }

        return back()->with('error', 'Failed to send reply');
    }
}
