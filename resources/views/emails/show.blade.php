@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Email Details</h5>
                    <a href="{{ route('emails.index') }}" class="btn btn-sm btn-outline-secondary">
                        Back to Inbox
                    </a>
                </div>

                <div class="card-body">
                    <div class="email-header mb-4">
                        <h2>{{ $email->subject }}</h2>
                        <div class="d-flex justify-content-between mt-2">
                            <div>
                                <strong>From:</strong> {{ $email->from_name }} &lt;{{ $email->from_email }}&gt;
                            </div>
                            <div>
                                <span class="badge {{ $email->status == 'completed' ? 'bg-success' : ($email->status == 'in_progress' ? 'bg-warning' : 'bg-primary') }}">
                                    {{ ucfirst(str_replace('_', ' ', $email->status)) }}
                                </span>
                            </div>
                        </div>
                        <div class="text-muted small mt-1">
                            <strong>Received:</strong> {{ $email->received_at->format('M j, Y g:i A') }}
                            ({{ $email->received_at->diffForHumans() }})
                        </div>
                        @if($email->assignee)
                        <div class="text-muted small mt-1">
                            <strong>Assigned To:</strong> {{ $email->assignee->name }}
                        </div>
                        @endif
                    </div>

                    <div class="email-body mb-4 p-3 border rounded">
                        {!! nl2br(e($email->body_preview)) !!}
                    </div>

                    @if($email->notes)
                    <div class="notes mb-4 p-3 bg-light border rounded">
                        <h5 class="text-muted">Notes</h5>
                        <p>{{ $email->notes }}</p>
                    </div>
                    @endif

                    @if($email->status == 'in_progress')
                    <div class="reply-section mt-4">
                        <h5>Reply to Email</h5>
                        <form action="{{ route('emails.reply', $email) }}" method="POST">
                            @csrf
                            <div class="form-group mb-3">
                                <textarea name="reply_content" rows="6" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send Reply</button>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
