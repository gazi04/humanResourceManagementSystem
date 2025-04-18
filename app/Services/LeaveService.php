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
            return DB::transaction(fn (): LengthAwarePaginator => DB::table('leave_types')
                ->select([
                    'name',
                    'description',
                    'isPaid',
                    'requiresApproval',
                    'isActive',
                ])
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
            throw new \RuntimeException('Lloji i pushimit nuk u gjet.', 404, $e);
        } catch (QueryException $e) {
            Log::error('Database error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Dështoi përditësimi i statusit të llojit të pushimit.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Unexpected error in toggleIsActive: '.$e->getMessage());
            throw new \RuntimeException('Ndodhi një gabim.', 500, $e);
        }

    }
}
