<?php

namespace App\Http\Controllers;

use App\Exports\EmailReportExport;
use App\Models\AssignedEmail;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function index()
    {
        $responseTimes = $this->reportService->getResponseTimeMetrics();
        $workloadDistribution = $this->reportService->getWorkloadDistribution();
        $emailVolume = $this->reportService->getEmailVolumeMetrics();

        return view('reports.index', compact('responseTimes', 'workloadDistribution', 'emailVolume'));
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:summary,detailed'
        ]);

        $report = $this->reportService->generateReport(
            Carbon::parse($validated['start_date']),
            Carbon::parse($validated['end_date']),
            $validated['report_type']
        );

        return view('reports.show', compact('report'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        return Excel::download(
            new EmailReportExport(
                Carbon::parse($validated['start_date']),
                Carbon::parse($validated['end_date'])
            ),
            'email-report-'.now()->format('Y-m-d').'.xlsx'
        );
    }
}
