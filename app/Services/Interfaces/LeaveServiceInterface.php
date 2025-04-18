<?php

namespace App\Services\Interfaces;

use App\Models\Leave\LeaveType;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeaveServiceInterface
{
    /**
     * 1. LEAVE TYPE FEATURES
     */
    public function getLeaveType(int $leaveTypeID): LeaveType;

    public function getLeaveTypes(): LengthAwarePaginator;

    public function createLeaveType(array $data): LeaveType;

    public function toggleIsActive(int $leaveTypeID): LeaveType;

    public function updateLeaveType(int $leaveTypeID, array $data): LeaveType;
}
