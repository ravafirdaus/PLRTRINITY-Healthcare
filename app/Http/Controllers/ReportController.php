<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    protected ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display reports dashboard.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::today()->format('Y-m-d'));

        $reportData = $this->reportService->getSummaryReport($startDate, $endDate);

        return view('reports.index', [
            'report' => $reportData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
