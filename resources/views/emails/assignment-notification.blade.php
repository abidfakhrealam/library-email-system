@component('mail::message')
# New Email Assignment

You have been assigned a new email to handle:

**Subject:** {{ $email->subject }}  
**From:** {{ $email->from_name }} <{{ $email->from_email }}>  
**Received:** {{ $email->received_at->format('M j, Y g:i a') }}  

@component('mail::button', ['url' => $url])
View Email
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
