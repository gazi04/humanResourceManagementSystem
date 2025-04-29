<?php

namespace App\Http\Controllers;

use App\Exceptions\EmployeeRetrievalException;
use App\Services\EmployeeService;
use App\Services\LeaveService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HumanResourceController extends Controller
{
    public function __construct(
        protected EmployeeService $employeeService,
        protected LeaveService $leaveService
    ) {}

    public function index(): View
    {
        $leaveTypes = $this->leaveService->getLeaveTypes();
        $todaysLeaveRequests = $this->leaveService->getTodaysLeaveRequests();

        return view('Hr.dashboard', [
            'leaveTypes' => $leaveTypes,
            'todaysLeaveRequests' => $todaysLeaveRequests,
        ]);
    }

    public function getHrs(): JsonResponse
    {
        try {
            $hrs = $this->employeeService->getHrs();

            return response()->json([
                'success' => true,
                'data' => $hrs->items(),
                'meta' => [
                    'total' => $hrs->total(),
                    'per_page' => $hrs->perPage(),
                    'current_page' => $hrs->currentPage(),
                    'last_page' => $hrs->lastPage(),
                ],
            ]);

        } catch (EmployeeRetrievalException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
