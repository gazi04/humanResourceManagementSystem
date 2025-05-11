<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveRequests\ApproveLeaveRequest;
use App\Http\Requests\LeaveRequests\CreateLeaveRequest;
use App\Http\Requests\LeaveRequests\RejectLeaveRequest;
use App\Services\LeaveService;
use App\Traits\AuthHelper;
use App\Traits\RedirectHelper;

class LeaveRequestController extends Controller
{
    use AuthHelper, RedirectHelper;

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
                'leaveTypeID',
                'startDate',
                'endDate',
                'durationType',
                'halfDayType',
                'reason',
            ]);

            $data['employeeID'] = $this->getLoggedUserID();

            $startDate = new \DateTime($data['startDate']);
            $endDate = new \DateTime($data['endDate']);
            $interval = $startDate->diff($endDate);
            $data['requestedDays'] = $interval->days + 1;

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

            return $this->toDashboard($request)->with('success', 'Kërkesa për pushim u dorëzua me sukses!');
        } catch (\RuntimeException) {
            return back()->with([
                'error' => 'Ndodhi një gabim gjatë dorëzimit të kërkesës. Ju lutem provoni përsëri.',
            ]);
        }
    }

    public function approve(ApproveLeaveRequest $request)
    {
        $leaveRequestId = $request->only('leaveRequestID');

        try {
            $this->leaveService->approveLeaveRequest($leaveRequestId['leaveRequestID']);

            return redirect()->route('hr.leave-request.index')
                ->with('success', 'Kërkesa e pushimit u miratua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-request.index')
                ->with('error', $e->getMessage());
        }
    }

    public function reject(RejectLeaveRequest $request)
    {
        $data = $request->only(['leaveRequestID', 'reason']);

        try {
            $this->leaveService->rejectRequest($data['leaveRequestID'], $data['reason']);

            return redirect()->route('hr.leave-request.index')
                ->with('success', 'Kërkesa e pushimit u refuzua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-request.index')
                ->with('error', $e->getMessage());
        }
    }
}
