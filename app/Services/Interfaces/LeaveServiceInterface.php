<?php

namespace App\Services\Interfaces;

use App\Models\Leave\LeaveType;

interface LeaveServiceInterface
{
    /**
     * 1. LEAVE TYPE FEATURES
     */
    public function createLeaveType(array $data): LeaveType;

    public function toggleIsActive(int $leaveTypeID): LeaveType;

    public function updateLeaveType(int $leaveTypeID, array $data): LeaveType;
}
