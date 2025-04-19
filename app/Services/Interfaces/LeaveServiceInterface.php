<?php

namespace App\Services\Interfaces;

use App\Models\Leave\LeavePolicy;
use App\Models\Leave\LeaveType;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeaveServiceInterface
{
    /**
     * 1. LEAVE TYPE FEATURES
     */
    public function getLeaveType(int $leaveTypeID): LeaveType;

    public function getLeaveTypes(): LengthAwarePaginator;

    public function createLeaveTypeWithPolicy(array $leaveTypeData, array $leavePolicyData): LeaveType;

    public function toggleIsActive(int $leaveTypeID): LeaveType;

    public function updateLeaveType(int $leaveTypeID, array $data): LeaveType;

    /**
     * 2. LEAVE POLICY FEATURES
     */
    public function getLeavePolicy(int $leavePolicyID): LeavePolicy;

    public function updateLeavePolicy(int $leavePolicyID, array $data): LeavePolicy;
}
