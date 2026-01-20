<?php

namespace App\Http\Controllers;

use App\Services\Statistics\LanguageClassStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageClassStatisticsController extends Controller
{
    public function __construct(protected LanguageClassStatisticsService $service)
    {
    }

    /**
     * Professor statistics
     */
    public function professors(Request $request): JsonResponse
    {
        $filters = $request->only(['professor_id', 'status', 'from', 'to']);
        $stats = $this->service->professorStatistics($filters);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Student statistics
     */
    public function students(Request $request): JsonResponse
    {
        $filters = $request->only(['student_id', 'status', 'from', 'to']);
        $stats = $this->service->studentStatistics($filters);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * (Optional) daily class statistics
     */
    public function dailyClasses(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'professor_id', 'student_id', 'from', 'to']);
        $period = $request->query('period', 'daily'); // can be daily, weekly, monthly

        $stats = $this->service->classesByPeriod($filters, $period);

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
