<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->middleware('auth');
        $this->reportService = $reportService;
    }

    /**
     * Display attendance trends report
     */
    public function attendanceTrends(Request $request): View
    {
        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->get('start_date'))
            : \Carbon\Carbon::now()->subMonths(6);

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->get('end_date'))
            : \Carbon\Carbon::now();

        $data = $this->reportService->getAttendanceTrends($startDate, $endDate);

        return view('reports.attendance-trends', compact('data', 'startDate', 'endDate'));
    }

    /**
     * Display member engagement report
     */
    public function memberEngagement(): View
    {
        $data = $this->reportService->getMemberEngagement();

        return view('reports.member-engagement', compact('data'));
    }

    /**
     * Display class performance report
     */
    public function classPerformance(): View
    {
        $data = $this->reportService->getClassPerformance();

        return view('reports.class-performance', compact('data'));
    }

    /**
     * Display mentorship success report
     */
    public function mentorshipSuccess(): View
    {
        $data = $this->reportService->getMentorshipSuccess();

        return view('reports.mentorship-success', compact('data'));
    }

    /**
     * Display reports index
     */
    public function index(): View
    {
        return view('reports.index');
    }
}

