<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employeers\CreateEmployeeRequest;
use App\Http\Requests\Employeers\DeleteEmployeeRequest;
use App\Http\Requests\Employeers\SearchEmployeeRequest;
use App\Http\Requests\Employeers\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Role;
use App\Services\EmployeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function __construct(protected EmployeeService $employeeService) {}

    public function index(): View
    {
        $result = $this->employeeService->getEmployees();

        return view('Admin.employee', ['employees' => $result]);
    }

    public function create(CreateEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->only('firstName', 'lastName', 'email', 'password', 'phone', 'hireDate', 'jobTitle', 'status', 'departmentID');
        $validated['password'] = Hash::make($validated['password']);

        $role = Role::where('roleID', $request->roleID)->first();

        if (! $role) {
            return redirect()->route('admin.employee.index')->with('error', 'Roli me këtë ID nuk egziston.');
        }

        $this->employeeService->createEmployee($role, $validated);

        return redirect()->route('admin.employee.index')->with('success', 'Punonjësi u krijua me sukses.');
    }

    public function update(UpdateEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->only('employeeID', 'firstName', 'lastName', 'email', 'password', 'phone', 'hireDate', 'jobTitle', 'status', 'departmentID');
        $employee = Employee::where('employeeID', $validated['employeeID'])->first();

        if (! $employee) {
            return redirect()->route('admin.employee.index')->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        $this->employeeService->updateEmployee($employee, $request);

        return redirect()->route('admin.employee.index')->with('success', 'Punonjësi u përditësua me sukses.');
    }

    public function destroy(DeleteEmployeeRequest $request): RedirectResponse
    {
        $validated = $request->only('employeeID', 'email');

        $employee = Employee::where('employeeID', $validated['employeeID'])
            ->where('email', $validated['email'])
            ->first();

        if (! $employee) {
            return redirect()->route('admin.employee.index')->with('error', 'Punonjësi nuk u gjet në bazën e të dhënave.');
        }

        $this->employeeService->deleteEmployee($employee);

        return redirect()->route('admin.employee.index')->with('success', 'Punonjësi është fshirë me sukses.');
    }

    public function search(SearchEmployeeRequest $request): View
    {
        $validated = $request->only('searchingTerm');
        $result = $this->employeeService->searchEmployees($validated['searchingTerm']);

        return view('Admin.employee', ['employees' => $result]);
    }
}
