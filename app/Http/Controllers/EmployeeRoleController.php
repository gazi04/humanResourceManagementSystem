<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateEmployeeRoleRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Services\EmployeeService;
use Illuminate\Http\Request;

class EmployeeRoleController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    public function update(UpdateEmployeeRoleRequest $request)
    {
        $validate = $request->only('employeeID', 'roleID');

        $employee = Employee::where('employeeID', $validate['employeeID'])->first();
        $role = Role::where('roleID', $validate['roleID'])->first();

        if(!$employee) {
            return redirect()->route('admin.employee.index')->with('error', 'Punonjësi nuk gjendet në bazën e të dhënave.');
        }

        if(!$role) {
            return redirect()->route('admin.employee.index')->with('error', 'Roli nuk gjendet në bazën e të dhënave.');
        }

        $this->employeeService->assignRole($employee, $role);
        return redirect()->route('admin.employee.index')->with('success', 'Roli i punonjësit u ndryshua me sukses.');
    }
}
