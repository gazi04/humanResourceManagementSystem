<?php

namespace App\Services;

use App\Models\Leave\LeaveType;
use App\Services\Interfaces\LeaveServiceInterface;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveService implements LeaveServiceInterface
{
    public function getLeaveTypes(): LengthAwarePaginator
    {
        return DB::transaction(fn(): LengthAwarePaginator => DB::table('leave_types')
            ->select([
                'name',
                'description',
                'isPaid',
                'requiresApproval',
                'isActive',
            ])
            ->paginate(10));
    }

    public function createLeaveType(array $data): LeaveType
    {
        try {
            return LeaveType::create($data);
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
            throw new \RuntimeException('LeaveType not found.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Failed to update LeaveType status.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('An error occurred.', 500, $e);
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
            throw new \RuntimeException('LeaveType not found.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Failed to update LeaveType status.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('An error occurred.', 500, $e);
        }

    }
}
