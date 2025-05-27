<?php

namespace App\Jobs;

use App\Mail\DailyReportMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendDailyReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $admins = User::where('is_admin', true)->get();
        $reportData = [
            'date' => now()->format('Y-m-d'),
            'stats' => [
                'total_emails' => AssignedEmail::whereDate('created_at', today())->count(),
                'unassigned' => AssignedEmail::whereDate('created_at', today())->where('status', 'unassigned')->count(),
                'completed' => AssignedEmail::whereDate('created_at', today())->where('status', 'completed')->count(),
                'avg_response_time' => AssignedEmail::whereDate('created_at', today())
                    ->where('status', 'completed')
                    ->average(\DB::raw('TIMESTAMPDIFF(HOUR, received_at, updated_at)'))
            ]
        ];

        foreach ($admins as $admin) {
            Mail::to($admin->email)
                ->send(new DailyReportMail($reportData));
        }
    }
}
