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
            return LeaveType::with(['policy' => function ($query) {
                $query->select([
                    'leavePolicyID',
                    'leaveTypeID',
                    'annualQuota',
                    'maxConsecutiveDays',
                    'allowHalfDay',
                    'probationPeriodDays',
                    'carryOverLimit',
                    'restricedDays',
                    'requirenments',
                ]);
            }])
                ->select([
                    'leaveTypeID',
                    'name',
                    'description',
                    'isPaid',
                    'requiresApproval',
                    'isActive',
                ])
                ->where('leaveTypeID', $leaveTypeID)
                ->firstOrFail();
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
        throw_if($this->isYearInitialized($year), new \RuntimeException("Bilancet e pushimeve për vitin {$year} janë inicializuar tashmë."));

        try {
            DB::transaction(function () use ($year): void {
                $employees = Employee::with('employeeRole')
                    ->where('status', 'Active')
                    ->get();

                $leaveTypes = LeaveType::with(['policy', 'roles'])
                    ->where('isActive', true)
                    ->get();

                foreach ($employees as $employee) {
                    $this->createBalanceForEmployee($employee, $leaveTypes, $year);
                }

                $this->markYearAsInitialized($year, $this->getLoggedUserID());
            });
        } catch (\Exception $e) {
            Log::error('Error initializing yearly balances: '.$e->getMessage());
            throw new \RuntimeException('Inicializimi i bilanceve vjetore të pushimeve dështoi.', 500, $e);
        }
    }

    public function deductDays(int $leaveBalanceID, float $days): LeaveBalance
    {
        try {
            return DB::transaction(function () use ($leaveBalanceID, $days): LeaveBalance {
                $leaveBalance = LeaveBalance::where('leaveBalanceID', $leaveBalanceID)->firstOrFail();
                throw_if($leaveBalance->remainingDays < $days, new \RuntimeException(
                    "Bilanc i pamjaftueshëm pushimesh. Në dispozicion: {$leaveBalance->remainingDays}, Kërkuar: {$days}"
                ));

                $leaveBalance->remainingDays -= $days;
                $leaveBalance->usedDays += $days;
                $leaveBalance->save();

                return $leaveBalance;
            });
        } catch (ModelNotFoundException $e) {
            Log::error('LeaveBalance not found: '.$e->getMessage());
            throw new \RuntimeException('Bilanci i pushimeve nuk u gjet.', 404, $e);
        } catch (\Exception $e) {
            Log::error('Failed to deduct leave days: '.$e->getMessage());
            throw new \RuntimeException('Dështoi zbritja e ditëve të pushimit.', $e->getCode(), $e);
        }
    }

    public function addDays(int $leaveBalanceID, float $days): LeaveBalance
    {
        try {
            return DB::transaction(function () use ($leaveBalanceID, $days): LeaveBalance {
                throw_if($days <= 0, new \RuntimeException('Days to add must be positive'));

                $leaveBalance = LeaveBalance::where('leaveBalanceID', $leaveBalanceID)->firstOrFail();

                // Prevent adding more days than were used
                $maxAddable = $leaveBalance->usedDays;
                $daysToAdd = min($days, $maxAddable);

                $leaveBalance->remainingDays += $daysToAdd;
                $leaveBalance->usedDays -= $daysToAdd;
                $leaveBalance->save();

                return $leaveBalance;
            });
        } catch (ModelNotFoundException $e) {
            Log::error('LeaveBalance not found: '.$e->getMessage());
            throw new \RuntimeException('Bilanci i pushimeve nuk u gjet.', 404, $e);
        } catch (\Exception $e) {
            Log::error('Failed to add leave days: '.$e->getMessage());
            throw new \RuntimeException('Dështoi shtimi i ditëve të pushimit.', $e->getCode(), $e);
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
            Log::error("Leave balance not found for employee {$employeeID}, leave type {$leaveTypeID}, year {$year}\nThis is the error message:".$e->getMessage());
            throw new \RuntimeException('Regjistri i bilancit të pushimeve nuk u gjet.', 404, $e);
        } catch (\Exception $e) {
            Log::error('Error retrieving leave balance: '.$e->getMessage());
            throw new \RuntimeException('Dështoi marrja e bilancit të pushimeve.', 500, $e);
        }
    }

    public function getBalances(int $employeeID): Collection
    {
        try {
            return LeaveBalance::where([
                'employeeID' => $employeeID,
                'year' => Carbon::now()->year,
            ])->get();
        } catch (ModelNotFoundException $e) {
            Log::error("Leave balance not found for employee {$employeeID}:".$e->getMessage());
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
        try {
            return DB::transaction(function () use ($leaveRequestID): LeaveRequest {
                /** @var LeaveRequest $leaveRequest */
                $leaveRequest = LeaveRequest::where('leaveRequestID', $leaveRequestID)
                    ->with('leaveBalance')
                    ->firstOrFail();

                // Validate leave balance exists
                if (! $leaveRequest->leaveBalance) {
                    throw new \RuntimeException('Nuk u gjet bilanc pushimi për këtë kërkesë.', 400);
                }

                $loggedUserID = $this->getLoggedUserID();

                // FIRST NEED TO APPROVE THE LEAVE REQUEST
                $leaveRequest->update([
                    'status' => 'approved',
                    'approvedBy' => $loggedUserID,
                    'approvedAt' => now(),
                ]);

                // SECOND NEEDS TO DEDUCT THE REQUESTED DAYS FROM THE LEAVE BALANCE
                /** @var LeaveBalance $leaveBalance */
                $leaveBalance = $leaveRequest->leaveBalance;

                $requestedDays = $this->calculateRequestedDays($leaveRequest);
                $remainingDays = $leaveBalance->remainingDays;
                $usedDays = $leaveBalance->usedDays;

                $leaveBalance->remainingDays = $remainingDays - $requestedDays;
                $leaveBalance->usedDays = $usedDays + $requestedDays;

                $leaveBalance->save();

                Log::info("The HR with ID { $loggedUserID } approved the leave request with ID { $leaveRequest->leaveRequestID }");

                return $leaveRequest->refresh();
            });
        } catch (ModelNotFoundException $e) {
            Log::error("LeaveRequest not found: {$leaveRequestID} - ".$e->getMessage());
            throw new \RuntimeException('Kërkesa për pushim nuk u gjet.', 404);
        } catch (QueryException $e) {
            Log::error("Database error approving leave request {$leaveRequestID}: ".$e->getMessage());
            throw new \RuntimeException('Dështoi miratimi i kërkesës së pushimit për shkak të një gabimi në bazën e të dhënave.', 500);
        } catch (\RuntimeException $e) {
            // Re-throw our custom exceptions with proper codes
            throw $e;
        } catch (\Exception $e) {
            Log::error("Unexpected error approving leave request {$leaveRequestID}: ".$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur gjatë miratimit të kërkesës së pushimit.', 500);
        }
    }

    public function rejectRequest(int $leaveRequestID, string $reason): LeaveRequest
    {
        try {
            return DB::transaction(function () use ($leaveRequestID, $reason): LeaveRequest {
                $loggedUserID = $this->getLoggedUserID();
                $leaveRequest = LeaveRequest::where('leaveRequestID', $leaveRequestID)->firstOrFail();
                $leaveRequest->update([
                    'status' => 'rejected',
                    'approvedBy' => $loggedUserID,
                    'approvedAt' => now(),
                    'rejectionReason' => $reason,
                ]);

                Log::info("The HR with ID { $loggedUserID } rejected the leave request with ID { $leaveRequest->leaveRequestID }");

                return $leaveRequest;
            });
        } catch (ModelNotFoundException $e) {
            Log::error("LeaveRequest not found: {$leaveRequestID} - ".$e->getMessage());
            throw new \RuntimeException('Kërkesa për pushim nuk u gjet.', 404);
        } catch (QueryException $e) {
            Log::error("Database error rejecting leave request {$leaveRequestID}: ".$e->getMessage());
            throw new \RuntimeException('Dështoi refuzimi i kërkesës së pushimit për shkak të një gabimi në bazën e të dhënave.', 500);
        } catch (\Exception $e) {
            Log::error("Unexpected error rejecting leave request {$leaveRequestID}: ".$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur gjatë refuzimit të kërkesës së pushimit.', 500);
        }
    }

    public function getTodaysLeaveRequests(): Collection
    {
        try {
            return LeaveRequest::where('created_at', Carbon::now()->year)->get();
        } catch (QueryException $e) {
            Log::error('Database error fetching todays leave requests: '.$e->getMessage());
            throw new \RuntimeException('Dështoi marrja e kërkesave të pushimit të sotshëm për shkak të një gabimi në bazën e të dhënave.', 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error fetching todays leave requests: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur gjatë marrjes së kërkesave të pushimit.', 500);
        }
    }

    public function getPendingLeaveRequests(): LengthAwarePaginator
    {
        try {
            return LeaveRequest::where('status', 'pending')
                ->orderBy('created_at')
                ->paginate(10);
        } catch (QueryException $e) {
            Log::error('Database error fetching pending leave requests: '.$e->getMessage());
            throw new \RuntimeException('Dështoi marrja e kërkesave të pushimit në pritje për shkak të një gabimi në bazën e të dhënave.', 500);
        } catch (\Exception $e) {
            Log::error('Unexpected error fetching pending leave requests: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur gjatë marrjes së kërkesave të pushimit.', 500);
        }
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

        Log::info("Created balance for employee {$employee->employeeID}, leave type {$leaveType->leaveTypeID}, year {$year}");
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
            'year' => Carbon::now()->year,
        ])->firstOrFail();

        if ($balance->remainingDays < $requestedDays) {
            throw new \Exception('Insufficient leave balance');
        }
    }

    public function calculateRequestedDays(LeaveRequest $leaveRequest): float
    {
        /** @var LeavePolicy $leavePolicy */
        /* $leavePolicy = $leaveRequest->policy(); */
        if ($leaveRequest->durationType === 'halfDay') {
            return 0.5;
        }

        $start = Carbon::parse($leaveRequest->startDate);
        $end = Carbon::parse($leaveRequest->endDate);

        // Exclude weekends
        return $start->diffInDaysFiltered(fn ($date): bool => ! $date->isWeekend(), $end) + 1; // Inclusive of start date
    }

    private function isYearInitialized(int $year): bool
    {
        return DB::table('leave_balance_initializations')
            ->where('year', $year)
            ->where('isInitialized', true)
            ->exists();
    }

    private function markYearAsInitialized(int $year, ?int $initiatorId): void
    {
        DB::table('leave_balance_initializations')->updateOrInsert(
            ['year' => $year],
            [
                'isInitialized' => true,
                'initializedAt' => now(),
                'initializedBy' => $initiatorId,
                'updated_at' => now(),
            ]
        );
    }
}
