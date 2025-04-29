<?php

namespace App\Services\Interfaces;

use App\Models\Employee;
use App\Models\Leave\LeaveBalance;
use App\Models\Leave\LeavePolicy;
use App\Models\Leave\LeaveRequest;
use App\Models\Leave\LeaveType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LeaveServiceInterface
{
    /**
     * 1. LEAVE TYPE FEATURES
     */
    public function getLeaveType(int $leaveTypeID): LeaveType;

    public function getLeaveTypes(): LengthAwarePaginator;

    public function createLeaveTypeWithPolicy(array $leaveTypeData, array $leavePolicyData, array $roles): LeaveType;

    public function toggleIsActive(int $leaveTypeID): LeaveType;

    public function updateLeaveType(int $leaveTypeID, array $data): LeaveType;

    /**
     * 2. LEAVE POLICY FEATURES
     */
    public function getLeavePolicy(int $leavePolicyID): LeavePolicy;

    public function updateLeavePolicy(int $leavePolicyID, array $data): LeavePolicy;

    /**
     * 3. LEAVE BALANCE FEATURES
     */
    public function initializeYearlyBalances(int $year): void;

    public function deductDays(LeaveBalance $leaveBalance, float $days): LeaveBalance;

    public function addDays(LeaveBalance $leaveBalance, float $days): LeaveBalance;

    public function getBalance(int $employeeID, int $leaveTypeID, int $year): LeaveBalance;

    public function getBalances(int $employeeID): Collection;

    public function createBalanceForEmployee(Employee $employee, Collection $leaveTypes, int $year): void;

    /*
    * 4. LEAVE REQUEST FEATURES
    */
    public function createLeaveRequest(array $data): LeaveRequest;

    public function approveLeaveRequest(int $leaveRequestID): LeaveRequest;

    public function rejectRequest(int $leaveRequestID, string $reason): LeaveRequest;

    public function getTodaysLeaveRequests(): Collection;
}
