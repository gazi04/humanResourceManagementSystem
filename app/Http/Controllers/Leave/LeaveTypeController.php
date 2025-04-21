<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeaveType\CreateLeaveTypeRequest;
use App\Http\Requests\LeaveType\IdValidationLeaveTypeRequest;
use App\Http\Requests\LeaveType\UpdateLeaveTypeRequest;
use App\Services\LeaveService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeaveTypeController extends Controller
{
    public function __construct(protected LeaveService $leaveService) {}

    /**
     * Display a listing of the leave types.
     */
    public function index(): View
    {
        try {
            return view('Hr.LeaveType.index',
                ['leaveTypes' => $this->leaveService->getLeaveTypes()]
            );
        } catch (\RuntimeException $e) {
            return view('Hr.LeaveType.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new leave type.
     */
    public function create(): View
    {
        return view('Hr.LeaveType.create');
    }

    /**
     * Store a newly created leave type in storage.
     */
    public function store(CreateLeaveTypeRequest $request): RedirectResponse
    {
        $leaveTypeData = $request->only([
            'name',
            'description',
            'isPaid',
            'requiresApproval',
            'isActive',
        ]);

        $leavePolicyData = $request->only([
            'annualQuota',
            'maxConsecutiveDays',
            'allowHalfDay',
            'probationPeriodDays',
            'carryOverLimit',
            'restricedDays',
            'requirenments',
        ]);

        $roles = $request->only(['roles']);

        try {
            $this->leaveService->createLeaveTypeWithPolicy($leaveTypeData, $leavePolicyData, $roles);

            return redirect()->route('hr.leave-type.index')->with('success', 'Lloji i pushimit u krijua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-type.edit')->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified leave type.
     */
    public function edit(IdValidationLeaveTypeRequest $request): View
    {
        $id = $request->only('leaveTypeID');

        try {
            return view('Hr.LeaveType.edit', [
                'leaveType' => $this->leaveService->getLeaveType($id['leaveTypeID']),
            ]);
        } catch (\RuntimeException $e) {
            return view('Hr.LeaveType.edit')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified leave type in storage.
     */
    public function update(UpdateLeaveTypeRequest $request): RedirectResponse
    {
        $id = $request->only('leaveTypeID');
        $data = $request->only(['name', 'description', 'isPaid', 'requiresApproval', 'isActive']);

        try {
            $this->leaveService->updateLeaveType($id['leaveTypeID'], $data);

            return redirect()->route('hr.leave-type.index')->with('success', 'Lloji i pushimit përditësohet me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-type.index')->with('error', $e->getMessage());
        }
    }

    public function toggleIsActive(IdValidationLeaveTypeRequest $request)
    {
        $validated = $request->only('leaveTypeID');
        $leaveType = $this->leaveService->toggleIsActive($validated['leaveTypeID']);

        try {
            return response()->json($leaveType, 200);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
