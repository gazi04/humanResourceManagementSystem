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

    public function create(): View
    {
        return view('Hr.LeaveType.create');
    }

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
            $this->leaveService->createLeaveTypeWithPolicy($leaveTypeData, $leavePolicyData, $roles['roles']);

            return redirect()->route('hr.leave-type.index')->with('success', 'Lloji i pushimit u krijua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-type.edit')->with('error', $e->getMessage());
        }
    }

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

    public function update(UpdateLeaveTypeRequest $request): RedirectResponse
    {
        $leaveTypeID = $request->only('leaveTypeID');
        $leaveTypeData = $request->only(['name', 'description', 'isPaid', 'requiresApproval']);

        $leavePolicyID = $request->only('leavePolicyID');
        $leavePolicyData = $request->only([
            'annualQuota',
            'maxConsecutiveDays',
            'allowHalfDay',
            'probationPeriodDays',
            'carryOverLimit',
            'restricedDays',
            'requirenments',
        ]);

        try {
            $this->leaveService->updateLeaveType($leaveTypeID['leaveTypeID'], $leaveTypeData);
            $this->leaveService->updateLeavePolicy($leavePolicyID['leavePolicyID'], $leavePolicyData);

            return redirect()->route('hr.leave-type.index')->with('success', 'Lloji i lejes me politikat e tij u përditësua me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-type.index')->with('error', $e->getMessage());
        }
    }

    public function toggleIsActive(IdValidationLeaveTypeRequest $request)
    {
        $validated = $request->only('leaveTypeID');

        try {
            $leaveType = $this->leaveService->toggleIsActive($validated['leaveTypeID']);

            return response()->json($leaveType, 200);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
