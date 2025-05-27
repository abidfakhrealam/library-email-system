<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && $this->shouldLog($request)) {
            ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => $request->method(),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'data' => $this->getRequestData($request)
            ]);
        }

        return $response;
    }

    protected function shouldLog(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE']);
    }

    protected function getRequestData(Request $request): array
    {
        $data = $request->all();
        
        // Remove sensitive data
        unset($data['password'], $data['password_confirmation'], $data['_token']);
        
        return $data;
    }
}
