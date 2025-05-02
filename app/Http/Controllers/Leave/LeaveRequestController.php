<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequests\ApproveLeaveRequest;
use App\Http\Requests\LeaveRequests\CreateLeaveRequest;
use App\Http\Requests\LeaveRequests\RejectLeaveRequest;
use App\Services\LeaveService;
use App\Traits\AuthHelper;
use Illuminate\Support\Facades\Log;

class LeaveRequestController extends Controller
{
    use AuthHelper;

    public function __construct(protected LeaveService $leaveService) {}

    public function index()
    {
        try {
            $leaveRequests = $this->leaveService->getPendingLeaveRequests();

            return view('Hr.LeaveRequest.index', [
                'leaveRequests' => $leaveRequests,
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function create()
    {
        $balances = $this->leaveService->getBalances($this->getLoggedUserID());

        return view('Employee.createLeaveRequest', [
            'employeeBalances' => $balances,
        ]);
    }

    public function store(CreateLeaveRequest $request)
    {
        try {
            $data = $request->only([
                'employeeID',
                'leaveTypeID',
                'startDate',
                'endDate',
                'durationType',
                'halfDayType',
                'requestedDays',
                'reason',
            ]);

            $data['employeeID'] = $this->getLoggedUserID();

            // Calculate requested days if not provided
            if (! isset($data['requestedDays']) || $data['requestedDays'] <= 0) {
                $data['requestedDays'] = $this->leaveService->calculateRequestedDays(
                    $data['startDate'],
                    $data['endDate'],
                    $data['durationType']
                );
            }

            // Check leave balance
            $balance = $this->leaveService->getBalance(
                $data['employeeID'],
                $data['leaveTypeID'],
                now()->year
            );

            if ($balance->remainingDays < $data['requestedDays']) {
                return back()->with('error', 'Nuk keni mjaftueshëm ditë pushimi të mbetura. Ditët e mbetura: '.$balance->remainingDays);
            }

            // Create the leave request
            $leaveRequest = $this->leaveService->createLeaveRequest($data);

            return redirect()->route('', $leaveRequest->leaveRequestID)
                ->with('success', 'Kërkesa për pushim u dorëzua me sukses!');

        } catch (\Exception $e) {
            Log::error('Error creating leave request: '.$e->getMessage());

            return back()->withErrors([
                'error' => 'Ndodhi një gabim gjatë dorëzimit të kërkesës. Ju lutem provoni përsëri.',
            ]);
        }
    }

    public function approve(ApproveLeaveRequest $request)
    {
        $leaveRequestId = $request->only('leaveTypeID');

        try {
            $this->leaveService->approveLeaveRequest($leaveRequestId['leaveTypeID']);

            return redirect()->route('hr.leave-request.index')
                ->with('success', 'Kërkesa e pushimit u miratua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-request.index')
                ->with('error', $e->getMessage());
        }
    }

    public function reject(RejectLeaveRequest $request)
    {
        $data = $request->only(['leaveTypeID', 'reason']);

        try {
            $this->leaveService->rejectRequest($data['leaveTypeID'], $data['reason']);

            return redirect()->route('hr.leave-request.index')
                ->with('success', 'Kërkesa e pushimit u refuzua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-request.index')
                ->with('error', $e->getMessage());
        }
    }
}
