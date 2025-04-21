<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Leave\LeavePolicy;
use App\Models\Leave\LeaveType;
use App\Models\Leave\LeaveTypeRole;
use App\Services\Interfaces\LeaveServiceInterface;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

class LeaveService implements LeaveServiceInterface
{
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
                ->orderBy('lt.create_at', 'desc')
                ->paginate(10));
        } catch (QueryException $e) {
            Log::error('Database error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Marrja e llojeve të pushimeve dështoi për shkak të një gabimi në bazën e të dhënave.', 500, $e);
        } catch (PDOException $e) {
            Log::error('PDO error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Lidhja me bazën e të dhënave dështoi.', 503, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in getLeaveTypes: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim i papritur.', 500, $e);
        }
    }

    public function createLeaveTypeWithPolicy(array $leaveTypeData, array $leavePolicyData, array $roles): LeaveType
    {
        try {
            return DB::transaction(function () use ($leaveTypeData, $leavePolicyData, $roles): LeaveType {
                /** @var LeaveType $leaveType */
                $leaveType = LeaveType::create($leaveTypeData);
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
            Log::error('LeaveType not found: '.$e->getMessage());
            throw new \RuntimeException('Lloji i pushimit nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in updateLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Dështoi përditësimi i statusit të llojit të pushimit.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in updateLeaveType: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }

    }

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
            return LeavePolicy::create($data);
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

    private function createBalanceForEmployee(Employee $employee, int $year) {}

    private function calculateInitialBalance() {}
}
