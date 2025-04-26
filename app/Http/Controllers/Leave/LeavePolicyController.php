<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Http\Requests\LeavePolicy\IdValidationLeavePolicyRequest;
use App\Http\Requests\LeavePolicy\UpdateLeavePoliciesRequest;
use App\Services\LeaveService;

class LeavePolicyController extends Controller
{
    public function __construct(protected LeaveService $leaveService) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdValidationLeavePolicyRequest $request)
    {
        $validated = $request->only('leavePolicyID');

        try {
            $leavePolicy = $this->leaveService->getLeavePolicy($validated['leavePolicyID']);

            return view('Hr.LeavePolicy.edit', [
                'leavePolicy' => $leavePolicy,
            ]);
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-type.index')->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeavePoliciesRequest $request)
    {
        $validated = $request->only('leavePolicyID');
        $data = $request->only([
            'annualQuota',
            'maxConsecutiveDays',
            'allowHalfDay',
            'probationPeriodDays',
            'carryOverLimit',
            'restricedDays',
            'requirenments',
        ]);

        try {
            $this->leaveService->updateLeavePolicy($validated['leavePolicyID'], $data);

            return redirect()->route('hr.leave-type.index')->with('success', 'Politikat e pushimit u përditësuan me sukses.');
        } catch (\RuntimeException $e) {
            return redirect()->route('hr.leave-policy.edit')->with('error', $e->getMessage());
        }
    }
}
