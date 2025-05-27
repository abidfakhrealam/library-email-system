<?php

namespace App\Services;

use App\Models\AssignedEmail;
use App\Models\Statistic;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportService
{
    public function generateDailyStatistics(): void
    {
        $today = Carbon::today();
        
        // Response times
        $avgResponseTime = AssignedEmail::whereDate('updated_at', $today)
            ->where('status', 'completed')
            ->avg(\DB::raw('TIMESTAMPDIFF(HOUR, received_at, updated_at)'));
        
        Statistic::updateOrCreate(
            ['metric' => 'avg_response_time', 'date' => $today],
            ['value' => $avgResponseTime ?? 0]
        );

        // Workload distribution
        $workload = AssignedEmail::whereDate('updated_at', $today)
            ->selectRaw('assigned_to, count(*) as count')
            ->groupBy('assigned_to')
            ->with('assignee')
            ->get()
            ->mapWithKeys(fn($item) => [$item->assignee->name => $item->count]);
            
        Statistic::updateOrCreate(
            ['metric' => 'workload_distribution', 'date' => $today],
            ['value' => $workload->sum(), 'dimensions' => $workload]
        );

        // Email volumes
        $volumes = [
            'total' => AssignedEmail::whereDate('received_at', $today)->count(),
            'unassigned' => AssignedEmail::whereDate('received_at', $today)
                ->where('status', 'unassigned')->count(),
            'completed' => AssignedEmail::whereDate('received_at', $today)
                ->where('status', 'completed')->count(),
        ];

        foreach ($volumes as $metric => $value) {
            Statistic::updateOrCreate(
                ['metric' => $metric . '_emails', 'date' => $today],
                ['value' => $value]
            );
        }
    }

    public function generateCustomReport(Carbon $startDate, Carbon $endDate, string $type): Collection
    {
        $query = AssignedEmail::with(['assignee', 'tags'])
            ->whereBetween('received_at', [$startDate, $endDate]);

        if ($type === 'summary') {
            return $query->selectRaw('
                DATE(received_at) as date,
                count(*) as total,
                sum(case when status = "completed" then 1 else 0 end) as completed,
                avg(case when status = "completed" then TIMESTAMPDIFF(HOUR, received_at, updated_at) else null end) as avg_response_time
            ')->groupBy('date')
            ->orderBy('date')
            ->get();
        }

        // Detailed report
        return $query->orderBy('received_at')->get();
    }

    public function getResponseTimeMetrics(int $days = 30): Collection
    {
        return Statistic::where('metric', 'avg_response_time')
            ->whereDate('date', '>=', Carbon::today()->subDays($days))
            ->orderBy('date')
            ->get(['date', 'value as avg_response_time']);
    }

    public function getWorkloadDistribution(): Collection
    {
        return Statistic::where('metric', 'workload_distribution')
            ->latest()
            ->limit(7)
            ->get(['date', 'value as total', 'dimensions as distribution']);
    }

    public function getEmailVolumeMetrics(int $days = 7): Collection
    {
        return Statistic::where('metric', 'like', '%_emails')
            ->whereDate('date', '>=', Carbon::today()->subDays($days))
            ->orderBy('date')
            ->get()
            ->groupBy('metric');
    }
}
