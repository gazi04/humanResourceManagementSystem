<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Leave\LeaveBalance;
use App\Models\Leave\LeavePolicy;
use App\Models\Leave\LeaveRequest;
use App\Models\Leave\LeaveType;
use App\Models\Leave\LeaveTypeRole;
use App\Services\Interfaces\LeaveServiceInterface;
use App\Traits\AuthHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

class LeaveService implements LeaveServiceInterface
{
    use AuthHelper;

    /**
     * 1. LEAVE TYPE FEATURES
     */
    public function getLeaveType(int $leaveTypeID): LeaveType
    {
        try {
            return LeaveType::where('leaveTypeID', $leaveTypeID)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("LeaveType not found: ID {$leaveTypeID}");
            throw new \RuntimeException('Lloji i pushimit nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error fetching LeaveType: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }
    }

    public function getLeaveTypes(): LengthAwarePaginator
    {
        try {
            return DB::transaction(fn (): LengthAwarePaginator => DB::table('leave_types as lt')
                ->leftJoin('leave_policies as lp', 'lt.leaveTypeID', '=', 'lp.leaveTypeID')
                ->select([
                    'lt.leaveTypeID',
                    'lt.name',
                    'lt.description',
                    'lt.isPaid',
                    'lt.requiresApproval',
                    'lt.isActive',
                    'lp.leavePolicyID',
                ])
                ->orderBy('lt.created_at', 'desc')
                ->paginate(10));
        } catch (QueryException $e) {
            Log::error('Database error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Marrja e llojeve të pushimeve dështoi për shkak të një gabimi në bazën e të dhënave.', 500, $e);
        } catch (PDOException $e) {
            Log::error('PDO error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Lidhja me bazën e të dhënave dështoi.', 503, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur në marrjen e llojeve të pushimeve.', 500, $e);
        }
    }

    public function createLeaveTypeWithPolicy(array $leaveTypeData, array $leavePolicyData, array $roles): LeaveType
    {
        try {
            return DB::transaction(function () use ($leaveTypeData, $leavePolicyData, $roles): LeaveType {
                /** @var LeaveType $leaveType */
                $leaveType = LeaveType::create($leaveTypeData);
                $leavePolicyData['leaveTypeID'] = $leaveType->leaveTypeID;
                $this->createLeavePolicy($leavePolicyData);

                foreach ($roles as $role) {
                    LeaveTypeRole::create([
                        'leaveTypeID' => $leaveType->leaveTypeID,
                        'roleID' => $role,
                    ]);
                }

                return $leaveType;
            });
        } catch (MassAssignmentException $e) {
            Log::error('MassAssignmentException in createLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Ofrohen fusha të pavlefshme.', 500, $e);
        } catch (QueryException $e) {
            Log::error('QueryException in createLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in createLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Krijimi i llojit të lejes dështoi.', 500, $e);
        }
    }

    public function toggleIsActive(int $leaveTypeID): LeaveType
    {
        try {
            /** @var LeaveType $leaveType */
            $leaveType = LeaveType::where('leaveTypeID', $leaveTypeID)->firstOrFail();

            $leaveType->isActive = ! $leaveType->isActive;
            $leaveType->save();

            return $leaveType;
        } catch (ModelNotFoundException $e) {
            Log::error('LeaveType not found: '.$e->getMessage());
            throw new \RuntimeException('Lloji i pushimit nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Dështoi përditësimi i statusit të llojit të pushimit.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }
    }

    public function updateLeaveType(int $leaveTypeID, array $data): LeaveType
    {
        try {
            /** @var LeaveType $leaveType */
            $leaveType = LeaveType::where('leaveTypeID', $leaveTypeID)->firstOrFail();

            $leaveType->update($data);

            return $leaveType;
        } catch (ModelNotFoundException $e) {
            Log::error("LeaveType not found: ID { $leaveTypeID }");
            throw new \RuntimeException('Lloji i pushimit nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in updateLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Dështoi përditësimi i statusit të llojit të pushimit.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in updateLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }
    }

    /**
     * 2. LEAVE POLICY FEATURES
     */
    public function getLeavePolicy(int $leavePolicyID): LeavePolicy
    {
        try {
            return LeavePolicy::where('leavePolicyID', $leavePolicyID)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error("LeavePolicy not found: ID {$leavePolicyID}");
            throw new \RuntimeException('Politika e lënies nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error fetching LeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }
    }

    public function updateLeavePolicy(int $leavePolicyID, array $data): LeavePolicy
    {
        try {
            /** @var LeavePolicy $leavePolicy */
            $leavePolicy = LeavePolicy::where('leavePolicyID', $leavePolicyID)->firstOrFail();

            $leavePolicy->update($data);

            return $leavePolicy;
        } catch (ModelNotFoundException $e) {
            Log::error('LeavePolicy not found: '.$e->getMessage());
            throw new \RuntimeException('Leave policy not found.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in updateLeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Dështoi përditësimi i rregullave të llojit të pushimit.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in updateLeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }
    }

    private function createLeavePolicy(array $data): LeavePolicy
    {
        try {
            return DB::transaction(function () use ($data): LeavePolicy {
                return LeavePolicy::create($data);
            });
        } catch (MassAssignmentException $e) {
            Log::error('MassAssignmentException in createLeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Ofrohen fusha të pavlefshme.', 500, $e);
        } catch (QueryException $e) {
            Log::error('QueryException in createLeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in createLeavePolicy: '.$e->getMessage());
            throw new \RuntimeException('Krijimi i llojit të lejes dështoi.', 500, $e);
        }
    }

    /**
     * 3. LEAVE BALANCE FEATURES
     */
    public function initializeYearlyBalances(int $year): void
    {
        try {
            DB::transaction(function () use ($year): void {
                $employees = Employee::where('status', 'Active')->get();

                $leaveTypes = LeaveType::with(['policy', 'roles'])
                    ->where('isActive', true)
                    ->get();

                foreach ($employees as $employee) {
                    $this->createBalanceForEmployee($employee, $leaveTypes, $year);
                }
            });
        } catch (\Exception $e) {
            Log::error('Error initializing yearly balances: '.$e->getMessage());
            throw new \RuntimeException('Inicializimi i bilanceve vjetore të pushimeve dështoi.', 500, $e);
        }
    }

    public function deductDays(LeaveBalance $leaveBalance, float $days): LeaveBalance
    {
        try {
            return DB::transaction(function () use ($leaveBalance, $days): LeaveBalance {
                throw_if($leaveBalance->remainingDays < $days, new \RuntimeException(
                    "Insufficient leave balance. Available: {$leaveBalance->remainingDays}, Requested: {$days}"
                ));

                $leaveBalance->remainingDays -= $days;
                $leaveBalance->usedDays += $days;
                $leaveBalance->save();

                return $leaveBalance;
            });
        } catch (\Exception $e) {
            Log::error('Failed to deduct leave days: '.$e->getMessage());
            throw new \RuntimeException('Failed to deduct leave days: '.$e->getMessage(), 0, $e);
        }
    }

    public function addDays(LeaveBalance $leaveBalance, float $days): LeaveBalance
    {
        try {
            return DB::transaction(function () use ($leaveBalance, $days): LeaveBalance {
                throw_if($days <= 0, new \RuntimeException('Days to add must be positive'));

                // Prevent adding more days than were used
                $maxAddable = $leaveBalance->usedDays;
                $daysToAdd = min($days, $maxAddable);

                $leaveBalance->remainingDays += $daysToAdd;
                $leaveBalance->usedDays -= $daysToAdd;
                $leaveBalance->save();

                return $leaveBalance;
            });
        } catch (\Exception $e) {
            Log::error('Failed to add leave days: '.$e->getMessage());
            throw new \RuntimeException('Failed to add leave days: '.$e->getMessage(), 0, $e);
        }
    }

    public function getBalance(int $employeeID, int $leaveTypeID, int $year): LeaveBalance
    {
        try {
            $balance = LeaveBalance::where([
                'employeeID' => $employeeID,
                'leaveTypeID' => $leaveTypeID,
                'year' => $year,
            ])->firstOrFail();

            throw_unless($balance, new ModelNotFoundException('Leave balance not found'));

            return $balance;
        } catch (ModelNotFoundException $e) {
            Log::error("Leave balance not found for employee {$employeeID}, leave type {$leaveTypeID}, year {$year}");
            throw new \RuntimeException('Leave balance record not found', 404, $e);
        } catch (\Exception $e) {
            Log::error('Error retrieving leave balance: '.$e->getMessage());
            throw new \RuntimeException('Failed to retrieve leave balance', 500, $e);
        }
    }

    public function createBalanceForEmployee(Employee $employee, Collection $leaveTypes, int $year): void
    {
        // Skip if employee has no role assigned
        if (! $employee->employeeRole) {
            Log::warning("Employee {$employee->employeeID} has no role assigned");
            throw new \RuntimeException('Ka një punonjës pa rol.');
        }

        foreach ($leaveTypes as $leaveType) {
            // Check if employee's role has access to this leave type
            if (! $leaveType->roles->contains('roleID', $employee->employeeRole->roleID)) {
                continue;
            }

            // Check if employee has completed probation period
            if ($this->isOnProbation($employee, $leaveType)) {
                Log::info("Employee {$employee->employeeID} is on probation for leave type {$leaveType->leaveTypeID}");

                continue;
            }

            // Create or update the balance
            $this->createOrUpdateBalance($employee, $leaveType, $year);
        }
    }

    /*
    * 4. LEAVE REQUEST FEATURES
    */
    public function createLeaveRequest(array $data): LeaveRequest
    {
        try {
            return DB::transaction(function () use ($data): LeaveRequest {
                return LeaveRequest::create($data);
            });
        } catch (MassAssignmentException $e) {
            Log::error('MassAssignmentException in createLeaveRequest: '.$e->getMessage());
            throw new \RuntimeException('Ofrohen fusha të pavlefshme.', 500, $e);
        } catch (QueryException $e) {
            Log::error('QueryException in createLeaveRequest: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in createLeaveRequest: '.$e->getMessage());
            throw new \RuntimeException('Kërkesa për leje nuk u krijua.', 500, $e);
        }
    }

    public function approveLeaveRequest(int $leaveRequestID): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequestID): LeaveRequest {
            /** @var LeaveRequest $user */
            $leaveRequest = LeaveRequest::where('leaveRequestID', $leaveRequestID)
                ->with('leaveBalance')
                ->firstOrFail();

            $loggedUserID = $this->getLoggedUserID();

            // FIRST NEED TO APPROVE THE LEAVE REQUEST
            $leaveRequest->update([
                'status' => 'approved',
                'approvedBy' => $loggedUserID,
                'approvedAt' => now(),
            ]);

            // SECOND NEEDS TO DEDUCT THE REQUESTED DAYS FROM THE LEAVE BALANCE
            /** @var LeaveBalance $leaveBalance */
            $requestedDays = $this->calculateRequestedDays($leaveRequest);
            $remainingDays = $leaveBalance->remainingDays;
            $usedDays = $leaveBalance->usedDays;

            $leaveBalance->remainingDays = $remainingDays - $requestedDays;
            $leaveBalance->usedDays = $usedDays + $requestedDays;

            $leaveBalance->save();

            // THIRD STEP IS TO LOG THE INFORMATION WHO WHICH LEAVE REQUEST APPROVED
            Log::info("The HR with ID { $loggedUserID } approved the leave request with ID { $leaveRequest->leaveRequestID }");

            return $leaveRequest;
        });
    }

    public function rejectRequest(int $leaveRequestID, string $reason): LeaveRequest
    {
        return DB::transaction(function () use ($leaveRequestID, $reason): LeaveRequest {
            $loggedUserID = $this->getLoggedUserID();
            $leaveRequest = LeaveRequest::where('leaveRequestID', $leaveRequestID)->firstOrFail();
            $leaveRequest->update([
                'status' => 'rejected',
                'approvedBy' => $loggedUserID,
                'approvedAt' => now(),
                'rejectionReason' => $reason,
            ]);

            return $leaveRequest;
        });
    }

    private function isOnProbation(Employee $employee, LeaveType $leaveType): bool
    {
        // Skip probation check if no policy or probation period defined
        if (! $leaveType->relationLoaded('policy') || ! $leaveType->policy || ! $leaveType->policy->probationPeriodDays) {
            return false;
        }

        // Skip if employee has no hire date
        if (! $employee->hireDate) {
            Log::warning("Employee {$employee->employeeID} has no hire date");

            return true; // Treat as on probation
        }

        // Convert string to Carbon instance if needed
        $hireDate = is_string($employee->hireDate) ? Carbon::parse($employee->hireDate) : $employee->hireDate;
        $probationEndDate = $hireDate->addDays(
            $leaveType->policy->probationPeriodDays
        );

        return now()->lt($probationEndDate);
    }

    private function createOrUpdateBalance(Employee $employee, LeaveType $leaveType, int $year): void
    {
        $previousYear = $year - 1;

        // Find previous year's balance for carry-over calculation
        $previousBalance = LeaveBalance::where([
            'employeeID' => $employee->employeeID,
            'leaveTypeID' => $leaveType->leaveTypeID,
            'year' => $previousYear,
        ])->first();

        // Calculate new balance
        $annualQuota = $leaveType->policy->annualQuota ?? 0;
        $carryOverDays = $this->calculateCarryOver($previousBalance, $leaveType);
        $remainingDays = $annualQuota + $carryOverDays;

        LeaveBalance::updateOrCreate(
            [
                'employeeID' => $employee->employeeID,
                'leaveTypeID' => $leaveType->leaveTypeID,
                'year' => $year,
            ],
            [
                'remainingDays' => $remainingDays,
                'usedDays' => 0,
                'carriedOverDays' => $carryOverDays,
            ]
        );

        Log::debug("Created balance for employee {$employee->employeeID}, leave type {$leaveType->leaveTypeID}, year {$year}");
    }

    private function calculateCarryOver(?LeaveBalance $previousBalance, LeaveType $leaveType): float
    {
        // No carry-over if no previous balance or policy doesn't allow it
        if (! $previousBalance instanceof \App\Models\Leave\LeaveBalance || ! $leaveType->policy || $leaveType->policy->carryOverLimit <= 0) {
            return 0;
        }

        return min(
            $previousBalance->remainingDays,
            $leaveType->policy->carryOverLimit
        );
    }

    private function validateLeaveBalance(int $employeeID, int $leaveTypeID, float $requestedDays): void
    {
        $balance = LeaveBalance::where([
            'employeeID' => $employeeID,
            'leaveTypeID' => $leaveTypeID,
            'year' => now()->year,
        ])->firstOrFail();

        if ($balance->remainingDays < $requestedDays) {
            throw new \Exception('Insufficient leave balance');
        }
    }

    private function calculateRequestedDays(LeaveRequest $leaveRequest): float
    {
        /** @var LeavePolicy $leavePolicy */
        /* $leavePolicy = $leaveRequest->policy(); */
        if ($leaveRequest->durationType === 'halfDay') {
            return 0.5;
        }

        $start = Carbon::parse($leaveRequest->startDate);
        $end = Carbon::parse($leaveRequest->endDate);

        // Exclude weekends
        return $start->diffInDaysFiltered(function ($date) {
            return ! $date->isWeekend();
        }, $end) + 1; // Inclusive of start date
    }
}
