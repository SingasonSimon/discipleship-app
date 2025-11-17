<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Get attendance trends
     */
    public function attendanceTrends(Request $request)
    {
        $startDate = $request->filled('start_date')
            ? \Carbon\Carbon::parse($request->get('start_date'))
            : null;

        $endDate = $request->filled('end_date')
            ? \Carbon\Carbon::parse($request->get('end_date'))
            : null;

        $data = $this->reportService->getAttendanceTrends($startDate, $endDate);

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get member engagement
     */
    public function memberEngagement()
    {
        $data = $this->reportService->getMemberEngagement();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get class performance
     */
    public function classPerformance()
    {
        $data = $this->reportService->getClassPerformance();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Get mentorship success
     */
    public function mentorshipSuccess()
    {
        $data = $this->reportService->getMentorshipSuccess();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}

