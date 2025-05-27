<?php

namespace App\Http\Middleware;

use App\Models\AssignedEmail;
use Closure;
use Illuminate\Http\Request;

class CheckEmailAccess
{
    public function handle(Request $request, Closure $next)
    {
        $emailId = $request->route('email');
        $email = AssignedEmail::findOrFail($emailId);

        if (auth()->user()->is_admin || $email->assigned_to === auth()->id()) {
            return $next($request);
        }

        abort(403, 'You do not have access to this email');
    }
}
