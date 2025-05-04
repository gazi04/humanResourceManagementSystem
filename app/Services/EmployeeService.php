<?php

namespace App\Services;

use App\Exceptions\EmployeeRetrievalException;
use App\Http\Requests\Employeers\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Role;
use App\Services\Interfaces\EmployeeServiceInterface;
use Illuminate\Database\Eloquent\MassAssignmentException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmployeeService implements EmployeeServiceInterface
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function createEmployee(Role $role, array $data): EmployeeRole
    {
        return DB::transaction(function () use ($role, $data): EmployeeRole {
            /** @var Employee $employee */
            $employee = Employee::create($data);

            return EmployeeRole::create([
                'employeeID' => $employee->employeeID,
                'roleID' => $role->roleID,
            ]);
        });
    }

    public function updateEmployee(Employee $employee, UpdateEmployeeRequest $request): Employee
    {
        return DB::transaction(function () use ($employee, $request): Employee {
            DB::table('employees')->where('employeeID', $employee->employeeID)
                ->update($request->only(
                    'firstName',
                    'lastName',
                    'email',
                    'phone',
                    'jobTitle',
                    'status',
                    'departmentID',
                ));

            if ($request->has('password')) {
                DB::table('employees')->where('employeeID', $employee->employeeID)
                    ->update(['password' => Hash::make($request->password)]);
            }

            return $employee;
        });
    }

    public function deleteEmployee(Employee $employee): void
    {
        DB::transaction(function () use ($employee): void {
            EmployeeRole::where('employeeID', $employee->employeeID)->delete();

            $employee->delete();
        });
    }

    public function assignRole(Employee $employee, Role $role): void
    {
        DB::transaction(function () use ($employee, $role): void {
            DB::table('employee_roles')->where('employeeID', $employee->employeeID)
                ->update(['roleID' => $role->roleID]);
        });
    }

    public function selectEmployeesBasedOnRoles(int $roleID): LengthAwarePaginator
    {
        return DB::transaction(fn () => DB::table('employees as e')
            ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
            ->join('roles as r', 'er.roleID', '=', 'r.roleID')
            ->leftJoin('departments as d', 'e.departmentID', '=', 'd.departmentID')
            ->leftJoin('employees as s', 'e.supervisorID', '=', 's.employeeID')
            ->where('r.roleID', $roleID)
            ->select([
                'e.employeeID',
                'e.firstName',
                'e.lastName',
                'e.email',
                'e.phone',
                'e.hireDate',
                'e.jobTitle',
                'e.status',
                'd.departmentName',
                's.firstName as supervisorFirstName',
                's.lastName as supervisorLastName',
                'r.roleName',
            ])
            ->paginate(10));
    }

    public function getEmployees(): LengthAwarePaginator
    {
        return DB::transaction(fn () => DB::table('employees as e')
            ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
            ->join('roles as r', 'er.roleID', '=', 'r.roleID')
            ->leftJoin('departments as d', 'e.departmentID', '=', 'd.departmentID')
            ->leftJoin('employees as s', 'e.supervisorID', '=', 's.employeeID')
            ->select([
                'e.employeeID',
                'e.firstName',
                'e.lastName',
                'e.email',
                'e.phone',
                'e.hireDate',
                'e.jobTitle',
                'e.status',
                'd.departmentID',
                'd.departmentName',
                's.firstName as supervisorFirstName',
                's.lastName as supervisorLastName',
                'r.roleID',
                'r.roleName',
            ])
            ->paginate(10));
    }

    public function getHrs(): LengthAwarePaginator
    {
        try {
            return DB::transaction(fn () => DB::table('employees as e')
                ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
                ->join('roles as r', 'er.roleID', '=', 'r.roleID')
                ->where('r.roleName', 'hr')
                ->select([
                    'e.employeeID',
                    'e.firstName',
                    'e.lastName',
                    'e.email',
                    'e.phone',
                    'e.hireDate',
                    'e.jobTitle',
                    'e.status',
                ])
                ->paginate(15));
        } catch (QueryException $e) {
            Log::error('Failed to fetch HR employees', ['error' => $e->getMessage()]);
            throw new EmployeeRetrievalException('Could not retrieve HR employees');
        }
    }

    public function searchEmployees(string $searchTerm): LengthAwarePaginator
    {
        return DB::transaction(fn () => DB::table('employees as e')
            ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
            ->join('roles as r', 'er.roleID', '=', 'r.roleID')
            ->leftJoin('departments as d', 'e.departmentID', '=', 'd.departmentID')
            ->leftJoin('employees as s', 'e.supervisorID', '=', 's.employeeID')
            ->select([
                'e.employeeID',
                'e.firstName',
                'e.lastName',
                'e.email',
                'e.phone',
                'e.hireDate',
                'e.jobTitle',
                'e.status',
                'd.departmentID',
                'd.departmentName',
                's.firstName as supervisorFirstName',
                's.lastName as supervisorLastName',
                'r.roleID',
                'r.roleName',
            ])
            ->where(function ($query) use ($searchTerm): void {
                $query->where('e.firstName', 'like', "%$searchTerm%")
                    ->orWhere('e.lastName', 'like', "%$searchTerm%")
                    ->orWhere('e.email', 'like', "%$searchTerm%")
                    ->orWhere('e.phone', 'like', "%$searchTerm%")
                    ->orWhere('e.jobTitle', 'like', "%$searchTerm%")
                    ->orWhere('d.departmentName', 'like', "%$searchTerm%")
                    ->orWhere('r.roleName', 'like', "%$searchTerm%")
                    ->orWhere('s.firstName', 'like', "%$searchTerm%")
                    ->orWhere('s.lastName', 'like', "%$searchTerm%");
            })
            ->paginate(10));
    }

    public function getEmployee(int $employeeID): Employee
    {
        try {
            return Employee::where('employeeID', $employeeID)
                ->with('role', 'department', 'supervisor')
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            Log::error('Employee model isn\'t found:'.$e->getMessage());
            throw new \RuntimeException('Punonjësi nuk u gjet në bazën e të dhënave.', 500, $e);
        } catch (MassAssignmentException $e) {
            Log::error('getEmployee failed'.$e->getMessage());
            throw new \RuntimeException('Ofrohen fusha të pavlefshme.', 500, $e);
        } catch (QueryException $e) {
            Log::error('Can\'t find or return employee, database error: '.$e->getMessage());
            throw new \RuntimeException('Gabim në bazën e të dhënave.', 500, $e);
        } catch (\Exception $e) {
            Log::error('Can\'t find or return the employee: '.$e->getMessage());
            throw new \RuntimeException('Diçka shkoi keq.', 500, $e);
        }
    }

    public function searchManagers(string $searchTerm)
    {
        return DB::transaction(fn () => DB::table('employees as e')
            ->join('employee_roles as er', 'e.employeeID', '=', 'er.employeeID')
            ->join('roles as r', 'er.roleID', '=', 'r.roleID')
            ->leftJoin('departments as d', 'e.departmentID', '=', 'd.departmentID')
            ->select([
                'e.employeeID',
                'e.firstName',
                'e.lastName',
            ])
            ->where('r.roleID', '=', '4')
            ->where(function ($query) use ($searchTerm): void {
                $query->where('e.firstName', 'like', "%$searchTerm%")
                    ->orWhere('e.lastName', 'like', "%$searchTerm%")
                    ->orWhere('e.email', 'like', "%$searchTerm%")
                    ->orWhere('e.phone', 'like', "%$searchTerm%")
                    ->orWhere('e.jobTitle', 'like', "%$searchTerm%")
                    ->orWhere('d.departmentName', 'like', "%$searchTerm%");
            })
            ->get()
            ->toJson());
    }
}
